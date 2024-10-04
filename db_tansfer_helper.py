import json
from pprint import pprint

def getMeaning(definitions):
    meaning = ""
    for definition in definitions:
        meaning += f"&emsp; ({definition['partOfSpeech']}) {definition['definition']}; <br />"

    meaning = meaning.replace('"', '\\"')
    meaning = meaning.replace("'", "\\'")
    return meaning


words_list = {}
with open("new_vocab.json") as f:
    vocab = json.load(f)
    
    for word in vocab:
        word_list = [word['word'], word['approxMeaning'], getMeaning(word['definitions'])]
        words_list[word['word']] = word_list

# pprint(words_list)

# import csv
# with open('eggs.csv', 'w', newline='') as csvfile:
#     spamwriter = csv.writer(csvfile, delimiter='\t',
#                             quotechar='|', quoting=csv.QUOTE_MINIMAL)
#     for word_list in words_list:
#         spamwriter.writerow(word_list)

import sqlite3

db = sqlite3.connect('words.db')

for key in words_list:
    db.execute("INSERT INTO words2 (word, kind, last_practice, meaning) VALUES (?, ?, ?, ?)", (words_list[key][0], words_list[key][1], 0, words_list[key][2]))

db.commit()
db.close()

# db = sqlite3.connect('words.db')

# kinds = set()
# print(kinds)
# for word in words_list:
#     kinds.add(words_list[word][1])

# # pprint(kinds)
# db = sqlite3.connect('words.db')
# for kind in kinds:
#     print(kind)
#     db.execute(f"INSERT INTO word_kind2 (kind) VALUES (\"{kind}\")")

# db.commit()
# db.close()