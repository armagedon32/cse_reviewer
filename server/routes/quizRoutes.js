import express from 'express';
import { 
  getAllQuizzes, 
  getQuizById, 
  createQuiz, 
  updateQuiz, 
  deleteQuiz,
  getCategories 
} from '../controllers/quizController.js';

const router = express.Router();

router.get('/quizzes', getAllQuizzes);
router.get('/quizzes/:id', getQuizById);
router.post('/quizzes', createQuiz);
router.put('/quizzes/:id', updateQuiz);
router.delete('/quizzes/:id', deleteQuiz);
router.get('/categories', getCategories);

export default router;