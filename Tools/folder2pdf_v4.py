import os
from PyPDF2 import PdfWriter, PdfReader
import io
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter

# Global variables
file_number = 1
# List of file extensions to skip
skip_extensions = ['woff','woff2', 'ico', 'png']

def add_text_to_pdf(text, pagesize=letter):
    """
    Create a PDF page with the given text and return a PDF reader object for that page.
    """
    packet = io.BytesIO()
    c = canvas.Canvas(packet, pagesize=pagesize)
    text_lines = text.split('\n')
    start_y = pagesize[1] - 40  # Start near the top of the page, adjust for margin
    for line in text_lines:
        c.drawString(10, start_y, line)
        start_y -= 10  # Move down for the next line
        if start_y < 40:  # Check for new page
            c.showPage()
            start_y = pagesize[1] - 40
    c.save()
    packet.seek(0)
    # Load the packet as a PDF page
    new_pdf = PdfReader(packet)
    return new_pdf

def build_tree_view(path, indent=0, file_tree=[]):
    """
    Recursively build a tree view of the folder structure.
    """
    global file_number
    for item in sorted(os.listdir(path)):
        item_path = os.path.join(path, item)
        if os.path.isdir(item_path):
            file_tree.append("    " * indent + f"[Folder] {item}")
            build_tree_view(item_path, indent + 1, file_tree)
        else:
            # Check file extension and skip if in the skip list
            if item.split('.')[-1].lower() not in skip_extensions:
                file_tree.append("    " * indent + f"[File {file_number}] {item}")
                file_number += 1
    return file_tree

def process_files_to_pdf(pdf_writer, path, file_mapping={}, indent=0):
    """
    Process each file, adding its content to the PDF with a heading.
    """
    for item in sorted(os.listdir(path)):
        item_path = os.path.join(path, item)
        if os.path.isdir(item_path):
            process_files_to_pdf(pdf_writer, item_path, file_mapping, indent + 1)
        else:
            # Skip files with extensions in the skip list
            if item.split('.')[-1].lower() not in skip_extensions:
                file_heading = f"{file_mapping[item_path]} {item}\n\n"
                try:
                    with open(item_path, 'r', encoding='utf-8') as file:
                        file_content = file_heading + file.read()
                except UnicodeDecodeError:
                    file_content = file_heading + " [Cannot display binary file content]"
                new_pdf = add_text_to_pdf(file_content)
                for page in new_pdf.pages:
                    pdf_writer.add_page(page)

def create_pdf_from_folder(folder_path, output_pdf_path):
    global file_number
    pdf_writer = PdfWriter()
    file_tree = []
    file_mapping = {}
    
    # Build the tree view and file mapping
    build_tree_view(folder_path, 0, file_tree)
    file_number = 1  # Reset for file processing
    for line in file_tree:
        if "[File" in line:
            file_path = line[line.index(']') + 2:]  # Extract file path from line
            file_mapping[os.path.join(folder_path, file_path)] = f"[File {file_number}]"
            file_number += 1

    # Add the tree view to the PDF
    tree_view_text = "\n".join(file_tree)
    tree_view_pdf = add_text_to_pdf(tree_view_text, pagesize=(letter[0], max(1000, 20*len(file_tree))))
    for page in tree_view_pdf.pages:
        pdf_writer.add_page(page)

    # Process each file
    process_files_to_pdf(pdf_writer, folder_path, file_mapping)
    
    with open(output_pdf_path, "wb") as out_pdf_file:
        pdf_writer.write(out_pdf_file)

if __name__ == "__main__":
    folder_path = "public_html"  # Adjust to your actual folder path
    output_pdf_path = "donnotec_website_v1.pdf"  # This will save the PDF in a place you can download
    create_pdf_from_folder(folder_path, output_pdf_path)
    print(f"PDF created at {output_pdf_path}")
