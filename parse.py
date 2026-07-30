import json
import re

try:
    with open(r"C:\Users\91895\.gemini\antigravity-ide\brain\7fbe45cc-8d4f-44cf-95d2-a741c046496c\.system_generated\steps\105\content.md", 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the WIZ_global_data JSON
    match = re.search(r'window\.WIZ_global_data\s*=\s*(\{.*?\});', content, re.DOTALL)
    if match:
        data_str = match.group(1)
        data = json.loads(data_str)
        # Often the text is buried. Let's just find all large strings in the file that might be the prompt/response.
        # Alternatively, we can search for a string like "AF_initDataCallback" with index 1
        
    # Let's extract all long English sentences that aren't javascript code
    sentences = re.findall(r'([A-Z][a-zA-Z0-9\s,\.\'\"\;\:\!\?\-\_]{30,})', content)
    
    unique_sentences = list(set(sentences))
    unique_sentences.sort(key=len, reverse=True)
    
    # Print the top 20 longest sentences, hopefully the prompt/response is among them
    print("POSSIBLE CONTENT:")
    for s in unique_sentences[:20]:
        if "google" not in s.lower() and "gstatic" not in s.lower() and "window" not in s.lower():
            print(s[:300])

except Exception as e:
    print(f"Error: {e}")
