import { useLocation, Link } from 'react-router-dom';

function Results() {
  const location = useLocation();
  const { score, total, correct, results, quizTitle } = location.state || {};

  if (!location.state) {
    return (
      <div className="results">
        <h2>No Results Found</h2>
        <p>Please take a quiz first.</p>
        <Link to="/" className="btn">Go Home</Link>
      </div>
    );
  }

  return (
    <div className="results">
      <h2>Quiz Complete: {quizTitle}</h2>
      <div className="score">{score}%</div>
      <p>You got {correct} out of {total} questions correct</p>
      
      <div className="review">
        <h3>Review Your Answers:</h3>
        {results.map((result, idx) => (
          <div key={idx} className={`review-item ${result.isCorrect ? 'correct' : 'incorrect'}`}>
            <h4>Question {idx + 1}: {result.question}</h4>
            <p><strong>Your Answer:</strong> {result.options[result.selectedAnswer]}</p>
            {!result.isCorrect && (
              <p><strong>Correct Answer:</strong> {result.options[result.correctAnswer]}</p>
            )}
            {result.explanation && (
              <p><strong>Explanation:</strong> {result.explanation}</p>
            )}
          </div>
        ))}
      </div>
      
      <Link to="/" className="btn">Take Another Quiz</Link>
    </div>
  );
}

export default Results;