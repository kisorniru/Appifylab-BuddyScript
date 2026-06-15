FROM node:20-alpine AS build

WORKDIR /app

COPY frontend/package.json ./

RUN npm install --registry=https://registry.npmjs.org/ --no-audit --no-fund
RUN npm install -D vite @vitejs/plugin-react --registry=https://registry.npmjs.org/ --no-audit --no-fund

COPY frontend/ ./

ARG VITE_API_BASE_URL=http://localhost:8080/api/v1
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL

RUN ls -lah node_modules/.bin
RUN npm run build

FROM nginx:stable-alpine

COPY docker/frontend/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80