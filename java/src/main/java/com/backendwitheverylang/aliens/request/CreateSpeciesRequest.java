package com.backendwitheverylang.aliens.request;

import jakarta.validation.constraints.*;

public record CreateSpeciesRequest(
        @NotBlank(message = "Name is required")
        @Size(min = 1, max = 50, message = "Name must be between 1-50 characters")
        String name
) {
}
