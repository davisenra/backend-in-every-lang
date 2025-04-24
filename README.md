# Backend in Every Language

Because learning new languages is always fun.

**Requirements:**

- Any framework (or no framework)
- Any architecture
- Any type of storage
- /encounters – Report & manage UFO sightings
- /species – List known alien types
- Dockerfile
- Tests (if possible)

**Models**

Encounter

```json
{
  "id": 1337,
  "location": "Area 51",
  "description": "Bright lights moving erratically",
  "species": {
    "id": 5173,
    "name": "Greys"
  }
}
```

Species

```json
{
  "id": 5173,
  "name": "Greys"
}
```
