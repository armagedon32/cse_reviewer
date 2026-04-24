import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { api } from '../services/api';

function Quiz() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [quiz, setQuiz] = useState(null);
  const [currentQuestion, setCurrentQuestion] = useState(0);
  const [answers, setAnswers] = useState({});
  const [timeLeft, setTimeLeft] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    loadQuiz();
  }, [id]);

  useEffect(() => {
    if (quiz && timeLeft > 0) {
      const timer = setInterval(() => {
        setTimeLeft(prev => {
          if (prev <= 1) {
            clearInterval(timer);
            handleSubmit();
            return 0;
          }
          return prev - 1;
        });
      }, 1000);
      return () => clearInterval(timer);
    }
  }, [quiz, timeLeft]);

  const loadQuiz = async () => {
    try {
      const data = await api.getQuiz(id);
      setQuiz(data);
      setTimeLeft(data.timeLimit * 60);
    } catch (err) {
      setError('Failed to load quiz');
    } finally {
      setLoading(false);
    }
  };

  const handleAnswer = (optionIndex) => {
    setAnswers(prev => ({
      ...prev,
      [currentQuestion]: optionIndex
    }));
  };

  const handleNext = () => {
    if (currentQuestion < quiz.questions.length - 1) {
      setCurrentQuestion(prev => prev + 1);
    }
  };

  const handlePrev = () => {
    if (currentQuestion > 0) {
      setCurrentQuestion(prev => prev - 1);
    }
  };

  const handleSubmit = () => {
    let correct = 0;
    const results = quiz.questions.map((q, idx) => {
      const isCorrect = answers[idx] === q.correctAnswer;
      if (isCorrect) correct++;
      return {
        question: q.question,
        options: q.options,
        selectedAnswer: answers[idx],
        correctAnswer: q.correctAnswer,
        isCorrect,
        explanation: q.explanation
      };
    });

    const score = Math.round((correct / quiz.questions.length) * 100);
    navigate('/results', { state: { score, total: quiz.questions.length, correct, results, quizTitle: quiz.title } });
  };

  if (loading) return <div className="loading">Loading quiz...</div>;
  if (error) return <div className="error">{error}</div>;
  if (!quiz) return <div className="error">Quiz not found</div>;

  const question = quiz.questions[currentQuestion];
  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  return (
    <div className="quiz-container">
      <div className="quiz-header">
        <h2>{quiz.title}</h2>
        <div className="timer">Time: {formatTime(timeLeft)}</div>
      </div>

      <div className="question">
        <h3>Question {currentQuestion + 1} of {quiz.questions.length}</h3>
        <p>{question.question}</p>
      </div>

      <div className="options">
        {question.options.map((option, idx) => (
          <div
            key={idx}
            className={`option ${answers[currentQuestion] === idx ? 'selected' : ''}`}
            onClick={() => handleAnswer(idx)}
          >
            {option}
          </div>
        ))}
      </div>

      <div className="nav-buttons">
        <button 
          className="prev-btn" 
          onClick={handlePrev}
          disabled={currentQuestion === 0}
        >
          Previous
        </button>
        
        {currentQuestion === quiz.questions.length - 1 ? (
          <button className="submit-btn" onClick={handleSubmit}>
            Submit Quiz
          </button>
        ) : (
          <button className="next-btn" onClick={handleNext}>
            Next
          </button>
        )}
      </div>
    </div>
  );
}

export default Quiz;