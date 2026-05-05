import json
from pathlib import Path

# Fix encoding
p = Path('graphify-out/.graphify_detect.json')
try:
    d = p.read_text(encoding='utf-8-sig')
except UnicodeDecodeError:
    # try utf-16
    d = p.read_text(encoding='utf-16le')

# Re-write cleanly as standard utf-8 without BOM
p.write_text(d, encoding='utf-8')

data = json.loads(d)
print(f"Corpus: {data.get('total_files', 0)} files · ~{data.get('total_words', 0)} words")
for t in ['code', 'document', 'paper', 'image', 'video']:
    if t in data.get('files', {}) and len(data['files'][t]) > 0:
        print(f"  {t}: {len(data['files'][t])} files")
