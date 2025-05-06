package com.backendwitheverylang.aliens.response;

import java.time.Instant;

public class JsonApiResponse<T> {

    public record Meta(
            String timestamp
    ) {
    }

    public T data;
    public Meta meta;

    public static <T> JsonApiResponse<T> build() {
        return new JsonApiResponse<T>();
    }

    public JsonApiResponse() {
    }

    public JsonApiResponse(T data) {
        this.data = data;
    }

    public JsonApiResponse(T data, Meta metadata) {
        this.data = data;
        this.meta = metadata;
    }

    public static <T> JsonApiResponse<T> of(T data) {
        return new JsonApiResponse<T>()
                .withData(data)
                .withDefaultMetadata();
    }

    public JsonApiResponse<T> withData(T data) {
        this.data = data;

        return this;
    }

    public JsonApiResponse<T> withMetadata(Meta metadata) {
        this.meta = metadata;

        return this;
    }

    public JsonApiResponse<T> withDefaultMetadata() {
        this.meta = new Meta(Instant.now().toString());

        return this;
    }

}
