import requests
import json
from bs4 import BeautifulSoup

website = requests.get("https://en.wikipedia.org/wiki/Most_common_words_in_English").text

soup = BeautifulSoup(website, features="html.parser")

table = soup.table.td
table = soup.find_all("a", class_="extiw")

i = 0
data=[]
while i < 50:
    item = {
        "id": i,
        "text": table[i].text
    }
    data.append(item)
    i += 1
data.append({
    "id": i,
    "text": "it's"
})
i += 1
data.append({
    "id": i,
    "text": "its"
})

with open('public/data/commonEngWords.json', 'w') as json_file:
    json.dump(data, json_file)