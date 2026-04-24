import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import path from 'path';
import { fileURLToPath } from 'url';
import connectDB from './config/db.js';
import quizRoutes from './routes/quizRoutes.js';

dotenv.config();

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();

app.use(cors());
app.use(express.json());

// Serve static files from React build in production
if (process.env.NODE_ENV === 'production') {
  app.use(express.static(path.join(__dirname, '../client/dist')));
}

app.use('/api', quizRoutes);

app.get('/health', (req, res) => {
  res.json({ status: 'ok' });
});

// Serve React app for all non-API routes in production
if (process.env.NODE_ENV === 'production') {
  app.get('*', (req, res) => {
    res.sendFile(path.join(__dirname, '../client/dist/index.html'));
  });
}

const PORT = process.env.PORT || 5000;

// Start server first, then connect to MongoDB
const startServer = () => {
  app.listen(PORT, async () => {
    console.log(`Server running on port ${PORT}`);
    // Try to connect to MongoDB after server starts
    await connectDB();
  });
};

startServer();