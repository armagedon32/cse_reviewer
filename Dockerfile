FROM node:20-alpine

WORKDIR /app

# Copy and build client
COPY client/ ./client/
WORKDIR /app/client
RUN npm install
RUN npm run build

# Copy and setup server
WORKDIR /app
COPY server/ ./server/
WORKDIR /app/server
RUN npm install

WORKDIR /app

EXPOSE 5000

CMD ["node", "server/index.js"]