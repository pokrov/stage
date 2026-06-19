from copy import deepcopy
from pathlib import Path
from tempfile import NamedTemporaryFile
from zipfile import ZIP_DEFLATED, ZipFile

from lxml import etree


ROOT = Path(__file__).resolve().parents[1]
SOURCE = ROOT.parent / "saaaaad.docx"
TARGET = ROOT / "storage" / "app" / "templates" / "attestation.docx"

W = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
R = "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
NS = {"w": W, "r": R}


def replace_paragraph_text(paragraph, text: str) -> None:
    texts = paragraph.xpath(".//w:t", namespaces=NS)

    if texts:
        texts[0].text = text
        for node in texts[1:]:
            node.text = ""
        return

    run = etree.SubElement(paragraph, f"{{{W}}}r")
    text_node = etree.SubElement(run, f"{{{W}}}t")
    text_node.text = text


def main() -> None:
    if not SOURCE.exists():
        raise FileNotFoundError(f"Modèle source introuvable : {SOURCE}")

    TARGET.parent.mkdir(parents=True, exist_ok=True)

    with ZipFile(SOURCE) as source_zip:
        document = etree.fromstring(source_zip.read("word/document.xml"))
        body = document.find(f"{{{W}}}body")
        paragraphs = body.findall(f"{{{W}}}p")
        section_properties = document.xpath("//w:sectPr", namespaces=NS)

        if len(paragraphs) < 8 or not section_properties:
            raise RuntimeError("La structure du modèle Word est inattendue.")

        replace_paragraph_text(paragraphs[1], "ATTESTATION DE STAGE")
        replace_paragraph_text(paragraphs[6], "${attestation_text}")
        replace_paragraph_text(paragraphs[7], "${closing_text}")

        # Une attestation doit contenir uniquement les huit premiers paragraphes.
        # Le paragraphe suivant comportait un saut de page et produisait une page vide.
        for paragraph in paragraphs[8:]:
            body.remove(paragraph)

        final_section = body.find(f"{{{W}}}sectPr")
        first_section = section_properties[0]

        # Conserver le footer actuellement validé, mais reprendre la géométrie et
        # le header par défaut de la première section du document source.
        footer_references = [
            deepcopy(reference)
            for reference in final_section.findall(f"{{{W}}}footerReference")
        ]

        for child in list(final_section):
            final_section.remove(child)

        default_header = first_section.xpath(
            "./w:headerReference[@w:type='default']", namespaces=NS
        )
        if not default_header:
            raise RuntimeError("Le header principal du modèle est introuvable.")

        final_section.append(deepcopy(default_header[0]))
        for footer_reference in footer_references:
            final_section.append(footer_reference)

        for tag in ("pgSz", "pgMar", "cols", "docGrid"):
            element = first_section.find(f"{{{W}}}{tag}")
            if element is not None:
                final_section.append(deepcopy(element))

        xml = etree.tostring(
            document,
            xml_declaration=True,
            encoding="UTF-8",
            standalone=True,
        )

        with NamedTemporaryFile(delete=False, suffix=".docx") as temporary:
            temporary_path = Path(temporary.name)

        try:
            with ZipFile(temporary_path, "w", ZIP_DEFLATED) as target_zip:
                for item in source_zip.infolist():
                    data = xml if item.filename == "word/document.xml" else source_zip.read(item)
                    target_zip.writestr(item, data)

            temporary_path.replace(TARGET)
        finally:
            temporary_path.unlink(missing_ok=True)

    print(TARGET)


if __name__ == "__main__":
    main()
