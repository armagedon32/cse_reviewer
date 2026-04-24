import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import path from 'path';
import { fileURLToPath } from 'url';
import connectDB from './config/db.js';
import quizRoutes from './routes/quizRoutes.js';
import Quiz from './models/Quiz.js';

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

const seedData = async () => {
  try {
    const count = await Quiz.countDocuments();
    if (count === 0) {
      const sampleQuizzes = [
        {
          title: 'JavaScript Basics',
          category: 'Programming',
          timeLimit: 5,
          questions: [
            {
              question: 'Which keyword is used to declare a variable in JavaScript?',
              options: ['var', 'let', 'const', 'All of the above'],
              correctAnswer: 3,
              explanation: 'All three keywords (var, let, const) are used to declare variables in JavaScript.'
            },
            {
              question: 'What does console.log() do?',
              options: ['Logs to console', 'Prints on screen', 'Saves to file', 'Creates alert'],
              correctAnswer: 0,
              explanation: 'console.log() outputs a message to the web console.'
            },
            {
              question: 'Which is NOT a JavaScript data type?',
              options: ['Number', 'String', 'Boolean', 'Float'],
              correctAnswer: 3,
              explanation: 'JavaScript uses Number for both integers and floats. Float is not a separate type.'
            }
          ]
        },
        {
          title: 'Data Structures Fundamentals',
          category: 'Data Structures',
          timeLimit: 10,
          questions: [
            {
              question: 'What is the time complexity of accessing an element in an array by index?',
              options: ['O(1)', 'O(n)', 'O(log n)', 'O(n²)'],
              correctAnswer: 0,
              explanation: 'Array access by index is O(1) - constant time, as it uses direct memory addressing.'
            },
            {
              question: 'Which data structure follows LIFO (Last In First Out)?',
              options: ['Queue', 'Stack', 'Linked List', 'Tree'],
              correctAnswer: 1,
              explanation: 'Stack follows LIFO - the last element added is the first one to be removed.'
            },
            {
              question: 'What is the worst-case time complexity of searching in a binary search tree?',
              options: ['O(1)', 'O(log n)', 'O(n)', 'O(n log n)'],
              correctAnswer: 2,
              explanation: 'In worst case (skewed tree), BST search is O(n). A balanced BST gives O(log n).'
            }
          ]
        }
      ];
      
      await Quiz.insertMany(sampleQuizzes);
      console.log('Sample quizzes seeded successfully');
    }
  } catch (error) {
    console.error('Error seeding data:', error);
  }
};

const startServer = async () => {
  await connectDB();
  await seedData();
  app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
  });
};

startServer();