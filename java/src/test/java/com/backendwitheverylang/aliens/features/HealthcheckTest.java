package com.backendwitheverylang.aliens.features;

import com.backendwitheverylang.aliens.controller.HealthcheckController;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.test.web.servlet.MockMvc;

import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(HealthcheckController.class)
public class HealthcheckTest {

    @Autowired
    private MockMvc mockMvc;

    @Test
    void healthcheck_Returns200AndValidResponse() throws Exception {
        mockMvc.perform(get("/healthcheck"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.status").value(true))
                .andExpect(jsonPath("$.timestamp").exists());
    }

}
