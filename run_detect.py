import json
from graphify.detect import detect, _SKIP_DIRS
from pathlib import Path

_SKIP_DIRS.update({'node_modules', 'vendor', '.git'})

result = detect(Path('.'))
print(json.dumps(result))
