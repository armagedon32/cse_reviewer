import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { api } from '../services/api';

function CreateQuiz() {
  const navigate = useNavigate();
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(false);
  
  const [quiz, setQuiz] = useState({
    title: '',
    category: 'Programming',
    timeLimit: 10,
    questions: [{ question: '', options: ['', '', '', ''], correctAnswer: 0, explanation: '' }]
  });

  useEffect(() => {
    api.getCategories().then(setCategories).catch(console.error);
  }, []);

  const addQuestion = () => {
    setQuiz(prev => ({
      ...prev,
      questions: [...prev.questions, { question: '', options: ['', '', '', ''], correctAnswer: 0, explanation: '' }]
    }));
  };

  const updateQuestion = (idx, field, value) => {
    setQuiz(prev => {
      const questions = [...prev.questions];
      questions[idx] = { ...questions[idx], [field]: value };
      return { ...prev, questions };
    });
  };

  const updateOption = (qIdx, oIdx, value) => {
    setQuiz(prev => {
      const questions = [...prev.questions];
      const options = [...questions[qIdx].options];
      options[oIdx] = value;
      questions[qIdx].options = options;
      return { ...prev, questions };
    });
  };

  const removeQuestion = (idx) => {
    setQuiz(prev => ({
      ...prev,
      questions: prev.questions.filter((_, i) => i !== idx)
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    try {
      await api.createQuiz(quiz);
      navigate('/');
    } catch (err) {
      alert('Failed to create quiz');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="create-form">
      <h2>Create New Quiz</h2>
      
      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label>Quiz Title</label>
          <input 
            type="text" 
            value={quiz.title}
            onChange={e => setQuiz({...quiz, title: e.target.value})}
            required
            placeholder="Enter quiz title"
          />
        </div>

        <div className="form-group">
          <label>Category</label>
          <select 
            value={quiz.category}
            onChange={e => setQuiz({...quiz, category: e.target.value})}
          >
            {categories.map(cat => (
              <option key={cat} value={cat}>{cat}</option>
            ))}
            <option value="Programming">Programming</option>
            <option value="Data Structures">Data Structures</option>
            <option value="Database">Database</option>
            <option value="Networks">Networks</option>
            <option value="Operating Systems">Operating Systems</option>
          </select>
        </div>

        <div className="form-group">
          <label>Time Limit (minutes)</label>
          <input 
            type="number" 
            value={quiz.timeLimit}
            onChange={e => setQuiz({...quiz, timeLimit: parseInt(e.target.value)})}
            min="1"
            max="60"
          />
        </div>

        <h3>Questions</h3>
        {quiz.questions.map((q, qIdx) => (
          <div key={qIdx} className="question-editor">
            <div className="form-group">
              <label>Question {qIdx + 1}</label>
              <textarea 
                value={q.question}
                onChange={e => updateQuestion(qIdx, 'question', e.target.value)}
                required
                placeholder="Enter question"
              />
            </div>

            <div className="form-group">
              <label>Options</label>
              {q.options.map((opt, oIdx) => (
                <div key={oIdx} style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', marginBottom: '0.5rem' }}>
                  <input 
                    type="radio" 
                    name={`correct-${qIdx}`}
                    checked={q.correctAnswer === oIdx}
                    onChange={() => updateQuestion(qIdx, 'correctAnswer', oIdx)}
                  />
                  <input 
                    type="text"
                    value={opt}
                    onChange={e => updateOption(qIdx, oIdx, e.target.value)}
                    placeholder={`Option ${oIdx + 1}`}
                    required
                    style={{ flex: 1 }}
                  />
                </div>
              ))}
            </div>

            <div className="form-group">
              <label>Explanation (optional)</label>
              <textarea 
                value={q.explanation}
                onChange={e => updateQuestion(qIdx, 'explanation', e.target.value)}
                placeholder="Explanation for the correct answer"
              />
            </div>

            {quiz.questions.length > 1 && (
              <button type="button" className="btn btn-secondary" onClick={() => removeQuestion(qIdx)}>
                Remove Question
              </button>
            )}
          </div>
        ))}

        <button type="button" className="btn btn-secondary" onClick={addQuestion}>
          Add Another Question
        </button>

        <button type="submit" className="btn" disabled={loading} style={{ marginLeft: '1rem' }}>
          {loading ? 'Creating...' : 'Create Quiz'}
        </button>
      </form>
    </div>
  );
}

export default CreateQuiz;