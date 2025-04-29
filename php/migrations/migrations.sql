CREATE TABLE IF NOT EXISTS species
(
    id   INTEGER PRIMARY KEY,
    name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS encounters
(
    id          INTEGER PRIMARY KEY,
    location    TEXT    NOT NULL,
    description TEXT,
    species_id  INTEGER NOT NULL,
    FOREIGN KEY (species_id) REFERENCES species (id)
);