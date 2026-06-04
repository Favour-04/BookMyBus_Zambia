from pathlib import Path
from PyPDF2 import PdfReader

base = Path(r'C:\Users\Favour\Documents\CS301 Project')
files = [
    'BookMyBus Zambia Project Proposal.pdf',
    'Chapter4_BookMyBus_System_Design.pdf'
]

for fname in files:
    path = base / fname
    print('---', fname, '---')
    reader = PdfReader(path)
    for i, page in enumerate(reader.pages[:2], 1):
        text = page.extract_text() or ''
        print(f'PAGE {i} len={len(text)}')
        print(text[:1200].replace('\n', ' '))
        print()
