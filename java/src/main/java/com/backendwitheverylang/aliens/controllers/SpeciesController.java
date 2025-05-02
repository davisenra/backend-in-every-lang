package com.backendwitheverylang.aliens.controllers;

import com.backendwitheverylang.aliens.model.Species;
import com.backendwitheverylang.aliens.repository.SpeciesRepository;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
@RequestMapping("/api/species")
public class SpeciesController {

    private final SpeciesRepository repository;

    public SpeciesController(SpeciesRepository repository) {
        this.repository = repository;
    }

    @GetMapping
    public ResponseEntity<List<Species>> index() {
        List<Species> species = repository.findAll();
        return ResponseEntity.ok(species);
    }

}
