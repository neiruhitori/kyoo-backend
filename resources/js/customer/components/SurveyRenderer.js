import Card from './Card'
import Rating from './Rating'
import SurveyComponent from './SurveyComponent'

export default function SurveyRenderer({
    surveyData,
    booking,
    rating,
    setRating,
    handleFeedbackClick,
    isFeedbackSubmitted,
    t
}) {

    const renderDefault = () => (
        <Card style={{
            marginBottom: '1.5rem',
            padding: '1.625rem'
        }}>
            <p style={{ textAlign: 'center' }}>
                {t('How satisfied are you with our service?')}
            </p>

            <div style={{ marginTop: '1.125rem' }}>
                <Rating
                    rate={booking.rating || rating}
                    onRateClick={rate => {
                        if (!booking.rating) setRating(rate);
                    }}
                />
            </div>

            {!booking.rating && (
                <div style={{
                    textAlign: 'center',
                    marginTop: '1.125rem'
                }}>
                    <button
                        type="submit"
                        style={{
                            padding: '.625rem 1.125rem',
                            borderRadius: '14px',
                            color: '#FFFFFF',
                            backgroundColor: '#007EC6',
                            border: 'none'
                        }}
                        onClick={handleFeedbackClick}
                    >
                        {t('Send Feedback')}
                    </button>
                </div>
            )}
        </Card>
    );

    const renderNPS = () => {
        const firstQuestion = surveyData?.questions?.[0] || {};
        return (
            <Card style={{
                marginBottom: '1.5rem',
                padding: '1.625rem'
            }}>
                <p style={{ textAlign: 'center' }}>
                    {firstQuestion.question_text}
                </p>

                <div style={{ marginTop: '1.125rem' }}>
                    <SurveyComponent
                        rate={booking.rating || rating}
                        onRateClick={rate => {
                            if (!booking.rating) setRating(rate);
                        }}
                    />
                </div>

                {!booking.rating && (
                    <div style={{
                        textAlign: 'center',
                        marginTop: '1.125rem'
                    }}>
                        <button
                            type="submit"
                            style={{
                                padding: '.625rem 1.125rem',
                                borderRadius: '14px',
                                color: '#FFFFFF',
                                backgroundColor: '#007EC6',
                                border: 'none'
                            }}
                            onClick={handleFeedbackClick}
                        >
                            {t('Send Feedback')}
                        </button>
                    </div>
                )}
            </Card>
        );
    };

    const renderCSAT = () => {
        const questions = surveyData?.questions || [];
        if (booking.rating || isFeedbackSubmitted) {
            return (
                <div
                    style={{
                        backgroundColor: '#E8F4FD',
                        borderRadius: '6px',
                        color: '#005AA3',
                        padding: '1.2rem',
                        textAlign: 'center',
                        marginBottom: '1.125rem'
                    }}
                >
                    <h3 style={{ marginBottom: '0.2rem' }}>
                        {t('Thank you for answering!')}
                    </h3>
                    <h5>{t('Your answer has been submitted')}</h5>
                </div>
            );
        }


        return (
            <>
                {questions.map((v) => (
                    <Card
                        key={v.id}
                        style={{
                            marginBottom: '1.5rem',
                            padding: '1.625rem'
                        }}
                    >
                        <p style={{ textAlign: 'center' }}>
                            {t(v.question_text)}
                        </p>

                        <div style={{ marginTop: '1.125rem' }}>
                            <SurveyComponent
                                rate={booking.rating || rating[v.id] || 0}
                                onRateClick={(rateValue) => {
                                    if (!booking.rating && !isFeedbackSubmitted) {
                                        setRating(prev => ({
                                            ...prev,
                                            [v.id]: rateValue
                                        }));
                                    }
                                }}
                            />
                        </div>
                    </Card>
                ))}
                {!booking.rating && !isFeedbackSubmitted && (
                    <div
                        style={{
                            textAlign: 'center',
                            marginBottom: '1.125rem'
                        }}>
                        <button
                            type="submit"
                            style={{
                                padding: '.625rem 1.125rem',
                                borderRadius: '14px',
                                color: '#FFFFFF',
                                backgroundColor: '#007EC6',
                                border: 'none'
                            }}
                            onClick={handleFeedbackClick}
                        >
                            {t('Send Feedback')}
                        </button>
                    </div>
                )}
            </>
        );
    };

    const renderSurvey = () => {
        const type = surveyData?.type;
        switch (type) {
            case 'default':
                return renderDefault();
            case 'nps':
                return renderNPS();
            case 'csat':
                return renderCSAT();
            default:
                return null;
        }
    };

    return renderSurvey();
}
