FROM node:20-alpine

WORKDIR /app

# Copy entire repository
COPY . .

# Build client
WORKDIR /app/client
RUN npm install
RUN npm run build

# Setup server
WORKDIR /app/server
RUN npm install

WORKDIR /app

EXPOSE 5000

CMD ["node", "server/index.js"]