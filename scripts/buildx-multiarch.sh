#!/usr/bin/env bash
set -euo pipefail

IMAGE_NAME="${IMAGE_NAME:-php-cloud-sentry}"
IMAGE_TAG="${IMAGE_TAG:-8.3-apache}"
PLATFORMS="${PLATFORMS:-linux/amd64,linux/arm64}"
BUILDER_NAME="${BUILDER_NAME:-php-cloud-sentry-builder}"
PUSH_IMAGE="${PUSH_IMAGE:-true}"

if ! command -v docker >/dev/null 2>&1; then
  echo "docker is required" >&2
  exit 1
fi

if ! docker buildx version >/dev/null 2>&1; then
  echo "docker buildx is required" >&2
  exit 1
fi

echo "[1/4] Installing QEMU emulators (requires privileged Docker access)"
docker run --privileged --rm tonistiigi/binfmt --install all

echo "[2/4] Creating or selecting buildx builder: ${BUILDER_NAME}"
if ! docker buildx inspect "${BUILDER_NAME}" >/dev/null 2>&1; then
  docker buildx create --name "${BUILDER_NAME}" --driver docker-container --use
else
  docker buildx use "${BUILDER_NAME}"
fi

echo "[3/4] Bootstrapping builder"
docker buildx inspect --bootstrap >/dev/null

BUILD_ARGS=(
  --platform "${PLATFORMS}"
  -f docker/php-apache/Dockerfile
  -t "${IMAGE_NAME}:${IMAGE_TAG}"
  .
)

echo "[4/4] Building image from php:8.3-apache"
if [[ "${PUSH_IMAGE}" == "true" ]]; then
  docker buildx build "${BUILD_ARGS[@]}" --push
else
  docker buildx build "${BUILD_ARGS[@]}" --load
fi

echo "Build complete: ${IMAGE_NAME}:${IMAGE_TAG}"
