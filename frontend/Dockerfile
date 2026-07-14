FROM node:22-alpine

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .

EXPOSE 3000

ENV NUXT_PUBLIC_API_BASE=http://localhost:8000/api

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
