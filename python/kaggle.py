from pymongo import MongoClient
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score
import sys
import time

def train(collection, bool, nouvelle_fleur):

    start_time = time.time()
    cursor = collection.find(
        {},
        {
            "_id": 0,
            "Id": 1,
            "SepalLengthCm": 1,
            "SepalWidthCm": 1,
            "PetalLengthCm": 1,
            "PetalWidthCm": 1,
            "Species": 1
        }
    )
    end_time = time.time()  # Stop le chronomètre
    elapsed_time = end_time - start_time

    df = pd.DataFrame(list(cursor))

    X = df[["SepalLengthCm", "SepalWidthCm", "PetalLengthCm", "PetalWidthCm"]]
    y = df["Species"]
    ids = df["Id"]


    X_train, X_test, y_train, y_test, ids_train, ids_test = train_test_split(
        X, y, ids, test_size=0.3, random_state=42, stratify=y
    )

    rf = RandomForestClassifier(n_estimators=500,random_state=42)

    rf.fit(X_train, y_train)

    if bool:
        y_pred = rf.predict(X_test)

        print(f"Accuracy RandomForest : {accuracy_score(y_test, y_pred)} (Récupération des données en {elapsed_time:.5f} secondes)")
    else:
        prediction = rf.predict(nouvelle_fleur)
        print(f"La fleur est : {prediction} (Récupération des données en {elapsed_time:.5f} secondes)")


def fill(collection, path):
    
    df = pd.read_csv(path)

    # Conversion en dictionnaires (format MongoDB)
    data = df.to_dict(orient="records")

    start_time = time.time()
    # Insertion dans MongoDB
    result = collection.insert_many(data)

    end_time = time.time()  # Stop le chronomètre
    elapsed_time = end_time - start_time

    print(f"Import terminé avec succès ! Nombre d'entrées insérées : {len(result.inserted_ids)} en {elapsed_time:.5f} secondes")


def new(nb1, nb2, nb3, nb4):
    nouvelle_fleur = pd.DataFrame({
    "SepalLengthCm": [nb1],
    "SepalWidthCm": [nb2],
    "PetalLengthCm": [nb3],
    "PetalWidthCm": [nb4]
    })

    train(collection, False, nouvelle_fleur)


client = MongoClient("mongodb://admin:password@localhost:27017/")
db = client["ma_base"]
collection = db["Iris"]

commande = sys.argv[1]

match commande:
    case "-f":
        fill(collection, sys.argv[2]) if len(sys.argv) > 2 else print("Pas de fichier CSV")
    case "-n":
        new(sys.argv[2], sys.argv[3], sys.argv[4], sys.argv[5]) if len(sys.argv) == 6 else print("Il manque un argument")
    case "-t":
        train(collection, True, None)
    case "-d":
        result = collection.delete_many({})
        print(f"Collection vidée : {result.deleted_count} documents supprimés")
    case _:
        print("Commande inconnue")



