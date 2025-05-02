package com.backendwitheverylang.aliens.repository;

import com.backendwitheverylang.aliens.model.Species;
import org.springframework.data.mongodb.repository.MongoRepository;

public interface SpeciesRepository extends MongoRepository<Species, String> {
}
