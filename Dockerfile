FROM node:20-alpine

# Install client dependencies and build
WORKDIR /app/client
COPY client/ ./
RUN npm install
RUN npm run build

# Install server dependencies
WORKDIR /app/server
COPY server/ ./
RUN npm install

WORKDIR /app

EXPOSE 5000

CMD ["node", "server/index.js"]