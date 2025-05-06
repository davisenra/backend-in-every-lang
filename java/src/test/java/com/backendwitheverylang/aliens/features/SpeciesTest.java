package com.backendwitheverylang.aliens.features;

import com.backendwitheverylang.aliens.controller.SpeciesController;
import com.backendwitheverylang.aliens.model.Species;
import com.backendwitheverylang.aliens.repository.SpeciesRepository;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.http.MediaType;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.test.web.servlet.MockMvc;

import java.util.List;

import static org.hamcrest.Matchers.hasSize;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.when;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.jsonPath;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.header;

@WebMvcTest(SpeciesController.class)
public class SpeciesTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
    private SpeciesRepository speciesRepository;

    @Test
    void getAllSpecies_Returns200AndEmptyList() throws Exception {
        when(speciesRepository.findAll()).thenReturn(List.of());

        mockMvc.perform(get("/api/species"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(0)));
    }

    @Test
    void species_CreateSpeciesReturns201AndValidResponse() throws Exception {
        Species mockSpecies = new Species("Reptilians");

        when(speciesRepository.save(any(Species.class)))
                .thenReturn(mockSpecies);

        mockMvc.perform(post("/api/species")
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("""
                                {
                                  "name": "Reptilians"
                                }
                                """))
                .andExpect(status().isCreated())
                .andExpect(header().exists("Location"))
                .andExpect(jsonPath("$.name").value("Reptilians"));
    }

    @Test
    void deleteSpecies_Returns204() throws Exception {
        when(speciesRepository.existsById("valid_id")).thenReturn(true);

        mockMvc.perform(delete("/api/species/valid_id"))
                .andExpect(status().isNoContent());
    }

    @Test
    void deleteSpecies_Returns204WhenSpeciesDoesNotExists() throws Exception {
        when(speciesRepository.existsById("invalid_id")).thenReturn(false);

        mockMvc.perform(delete("/api/species/invalid_id"))
                .andExpect(status().isNoContent());
    }

}
