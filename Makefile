.PHONY: build-app
build-app:
	docker buildx build \
		-t f00b4r/nginx:app \
		--platform linux/arm64 \
		01-app

.PHONY: up-app
up-app:
	docker run \
		-it \
		--rm \
		-p 8000:80 \
		-p 8001:81 \
		-v $(CURDIR)/01-app/app:/srv \
		f00b4r/nginx:app
