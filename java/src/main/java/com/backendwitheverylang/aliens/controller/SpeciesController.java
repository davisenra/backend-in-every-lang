package com.backendwitheverylang.aliens.controller;

import com.backendwitheverylang.aliens.model.Species;
import com.backendwitheverylang.aliens.repository.SpeciesRepository;
import com.backendwitheverylang.aliens.request.CreateSpeciesRequest;
import com.backendwitheverylang.aliens.response.JsonApiResponse;
import jakarta.validation.Valid;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.support.ServletUriComponentsBuilder;

import java.net.URI;
import java.util.List;

@RestController
@RequestMapping("/api/species")
public class SpeciesController {

    private final SpeciesRepository repository;

    public SpeciesController(SpeciesRepository repository) {
        this.repository = repository;
    }

    @GetMapping
    public ResponseEntity<JsonApiResponse<List<Species>>> index() {
        List<Species> species = repository.findAll();

        return ResponseEntity.ok(JsonApiResponse.of(species));
    }

    @GetMapping("/{id}")
    public ResponseEntity<JsonApiResponse<Species>> find(@PathVariable String id) {
        return repository.findById(id)
                .map(species -> ResponseEntity.ok(JsonApiResponse.of(species)))
                .orElseGet(() -> ResponseEntity.notFound().build());
    }

    @PostMapping
    public ResponseEntity<JsonApiResponse<Species>> create(
            @Valid @RequestBody CreateSpeciesRequest payload
    ) {
        var species = new Species(payload.name());
        var savedSpecies = repository.save(species);

        URI location = ServletUriComponentsBuilder
                .fromCurrentRequest()
                .path("/{id}")
                .buildAndExpand(savedSpecies.getId())
                .toUri();

        return ResponseEntity
                .created(location)
                .body(JsonApiResponse.of(savedSpecies));
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Void> destroy(@PathVariable String id) {
        repository.deleteById(id);

        return ResponseEntity.noContent().build();
    }

}
