import json
import re

try:
    with open(r"C:\Users\91895\.gemini\antigravity-ide\brain\7fbe45cc-8d4f-44cf-95d2-a741c046496c\.system_generated\steps\105\content.md", 'r', encoding='utf-8') as f:
        content = f.read()

    strings = re.findall(r'"([^"\\]*(?:\\.[^"\\]*)*)"', content)
    
    candidates = []
    for s in strings:
        s = s.replace('\\"', '"').replace('\\n', '\n')
        if len(s) > 100 and ('<' in s or ' ' in s):
            if not s.startswith('http') and 'function(' not in s and 'AF_init' not in s and len(s.split(' ')) > 5:
                candidates.append(s)
                
    with open('candidates.txt', 'w', encoding='utf-8') as out:
        for c in list(set(candidates)):
            out.write("CANDIDATE:\n" + c + "\n\n")

    print("Success")
except Exception as e:
    print(f"Error: {e}")
