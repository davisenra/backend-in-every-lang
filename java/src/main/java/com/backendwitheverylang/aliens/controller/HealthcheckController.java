package com.backendwitheverylang.aliens.controller;

import org.springframework.http.ResponseEntity;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.GetMapping;

import java.util.Map;

@Controller
public class HealthcheckController {

    @GetMapping("/healthcheck")
    public ResponseEntity<Map<String, Object>> healthcheck() {
        return ResponseEntity.ok()
                .body(Map.of(
                        "status", true,
                        "timestamp", System.currentTimeMillis()
                ));
    }

}
