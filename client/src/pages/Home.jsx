import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { api } from '../services/api';

function Home() {
  const [quizzes, setQuizzes] = useState([]);
  const [categories, setCategories] = useState([]);
  const [selectedCategory, setSelectedCategory] = useState('All');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      const [quizzesData, categoriesData] = await Promise.all([
        api.getQuizzes(),
        api.getCategories()
      ]);
      setQuizzes(quizzesData);
      setCategories(['All', ...categoriesData]);
    } catch (err) {
      setError('Failed to load quizzes');
    } finally {
      setLoading(false);
    }
  };

  const filteredQuizzes = selectedCategory === 'All' 
    ? quizzes 
    : quizzes.filter(q => q.category === selectedCategory);

  if (loading) return <div className="loading">Loading...</div>;

  return (
    <div>
      <h2 className="home-title">Available Quizzes</h2>
      
      {error && <div className="error">{error}</div>}
      
      <div className="category-filter">
        {categories.map(cat => (
          <button
            key={cat}
            className={`category-btn ${selectedCategory === cat ? 'active' : ''}`}
            onClick={() => setSelectedCategory(cat)}
          >
            {cat}
          </button>
        ))}
      </div>

      {filteredQuizzes.length === 0 ? (
        <div className="loading">No quizzes found</div>
      ) : (
        <div className="quiz-list">
          {filteredQuizzes.map(quiz => (
            <div key={quiz._id} className="quiz-card">
              <h3>{quiz.title}</h3>
              <span className="category">{quiz.category}</span>
              <div className="meta">
                <span>{quiz.questions.length} questions</span>
                <span>{quiz.timeLimit} min</span>
              </div>
              <Link to={`/quiz/${quiz._id}`}>Start Quiz →</Link>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}

export default Home;