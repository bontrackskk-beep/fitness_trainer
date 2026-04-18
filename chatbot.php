<?php
function get_chatbot_response($message, $context = []) {
    $message = strtolower(trim($message));
    $user_goal = $context['user_goal'] ?? '';
    $is_logged_in = $context['is_logged_in'] ?? false;
    
    $responses = [
        'greeting' => [
            'patterns' => ['hello', 'hi', 'hey', 'help', 'start', 'what can you do', 'who are you'],
            'response' => "Hi! I'm your Zen Fit assistant. I can help you with:\n\n• Understanding features (goals, workouts, diet, trainers, progress)\n• Navigating the app\n• Suggestions based on your fitness journey\n• Answering questions about the platform\n\nJust ask me anything!",
            'suggestions' => ['How do I set a goal?', 'Show me workouts', 'How do I book a trainer?']
        ],
        'goals' => [
            'patterns' => ['goal', 'set goal', 'change goal', 'fitness goal', 'weight loss', 'weight gain', 'muscle'],
            'response' => "Your goal shapes your entire dashboard experience. Here's how goals work:\n\n• Go to Goal Page to select your focus\n• Options: Weight Loss, Weight Gain, Fitness, Yoga, Muscle Gain\n• The dashboard updates to match your goal\n• You can change it anytime\n\nCurrently: " . ($user_goal ?: 'Not set yet'),
            'suggestions' => ['How do I track progress?', 'What workouts match Weight Loss?']
        ],
        'dashboard' => [
            'patterns' => ['dashboard', 'home', 'main', 'overview', 'stats'],
            'response' => "Your dashboard shows:\n\n• Current goal and focus insight\n• Weight & calorie tracking\n• Progress charts\n• Workout suggestions\n• Weekly targets\n• Quick actions for workouts, diet, trainers, progress\n\nIt's your command center for fitness!",
            'suggestions' => ['How do I log weight?', 'Show trainer options']
        ],
        'workouts' => [
            'patterns' => ['workout', 'exercise', 'training', 'routine', 'session', 'gym'],
            'response' => "The Workout Library contains:\n\n• Various training routines\n• Difficulty levels (Beginner, Intermediate, Advanced)\n• Duration estimates\n• Exercise descriptions\n• Video links for some routines\n• Goals alignment (Weight Loss, Muscle Gain, Yoga, etc.)\n\nBrowse and pick sessions matching your energy!",
            'suggestions' => ['How do I choose a workout?', 'What is HIIT?']
        ],
        'diet' => [
            'patterns' => ['diet', 'nutrition', 'meal', 'food', 'eat', 'calories', 'plan'],
            'response' => "Diet Plans help you eat for your goals:\n\n• Different plans for different goals\n• Calorie targets (kcal)\n• Meal schedules\n• Aligned with your selected goal\n\nCheck Diet page for available plans!",
            'suggestions' => ['How many calories for weight loss?', 'Show me meal plans']
        ],
        'trainers' => [
            'patterns' => ['trainer', 'coach', 'book', 'booking', 'session'],
            'response' => "Trainers help you with:\n\n• Browse available trainers\n• View specialization & experience\n• Check ratings (out of 5)\n• See pricing\n• Book a session for specific date/time\n\nGo to Trainers page to compare and book!",
            'suggestions' => ['How much do trainers cost?', 'Can I cancel a booking?']
        ],
        'progress' => [
            'patterns' => ['progress', 'track', 'weight', 'calories', 'log', 'history', 'chart'],
            'response' => "Progress tracking shows:\n\n• Weight entries over time\n• Calories burned\n• Visual charts\n• Weekly trends\n• Historical data table\n\nLog regularly for accurate tracking!",
            'suggestions' => ['How often should I log?', 'What do the charts show?']
        ],
        'login_register' => [
            'patterns' => ['login', 'register', 'sign up', 'sign in', 'account', 'create account'],
            'response' => "Getting started:\n\n• Register a new account\n• Set your fitness goal\n• Login to access dashboard\n• All features require login\n\nNew users should register first!",
            'suggestions' => ['Is it free?', 'How do I reset password?']
        ],
        'navigation' => [
            'patterns' => ['how do i', 'where', 'go to', 'navigate', 'find', 'page'],
            'response' => "Navigation tips:\n\n• Dashboard: Main hub after login\n• Goal Page: Set/change your focus\n• Workouts: Browse training routines\n• Diet: View nutrition plans\n• Trainers: Find & book coaches\n• Progress: Log weight/calories\n\nUse the top navigation menu!",
            'suggestions' => ['Take me to workouts', 'I want to track progress']
        ],
        'suggestions' => [
            'patterns' => ['suggest', 'recommend', 'what should', 'help me', 'advice'],
            'response' => ($is_logged_in) ? 
                "Here are personalized suggestions:\n\n1. Set your goal if not done yet\n2. Browse workouts matching your goal\n3. Consider a trainer for guidance\n4. Log progress regularly\n5. Check diet plans for nutrition" :
                "Here are suggestions:\n\n1. Register an account\n2. Set your fitness goal\n3. Explore the dashboard after login\n4. Browse workouts and diet plans",
            'suggestions' => ['Take me to register', 'Show me around']
        ],
        'default' => [
            'patterns' => [],
            'response' => "I'm here to help! Ask me about:\n\n• Goals & setting them\n• Workouts & exercises\n• Diet & nutrition\n• Trainers & booking\n• Progress tracking\n• Navigating the app\n\nWhat would you like to know?",
            'suggestions' => ['How do I use this app?', 'Show me the dashboard']
        ]
    ];
    
    foreach ($responses as $key => $data) {
        foreach ($data['patterns'] as $pattern) {
            if (strpos($message, $pattern) !== false) {
                return $data;
            }
        }
    }
    
    return $responses['default'];
}
?>
<style>
.chatbot-toggle {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffb347, #ff7a18);
    border: none;
    cursor: pointer;
    box-shadow: 0 8px 30px rgba(255, 122, 24, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: #111;
    z-index: 9999;
    transition: transform 0.2s ease;
}
.chatbot-toggle:hover {
    transform: scale(1.08);
}
.chatbot-window {
    position: fixed;
    bottom: 94px;
    right: 24px;
    width: min(380px, calc(100% - 48px));
    max-height: 500px;
    background: #0a1521;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 9998;
}
.chatbot-window.open {
    display: flex;
}
.chatbot-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #ffb347, #ff7a18);
    display: flex;
    align-items: center;
    gap: 12px;
}
.chatbot-header span {
    font-size: 26px;
}
.chatbot-header div {
    flex: 1;
}
.chatbot-header h3 {
    margin: 0;
    font-size: 16px;
    color: #111;
    font-weight: 700;
}
.chatbot-header p {
    margin: 2px 0 0;
    font-size: 12px;
    color: rgba(0, 0, 0, 0.6);
}
.chatbot-header button {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #111;
    padding: 4px;
}
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.chatbot-message {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.5;
}
.chatbot-message.bot {
    background: rgba(255, 255, 255, 0.08);
    color: #f4f7fb;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
}
.chatbot-message.user {
    background: linear-gradient(135deg, #ffb347, #ff7a18);
    color: #111;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}
.chatbot-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 8px;
}
.chatbot-suggestions button {
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(255, 255, 255, 0.05);
    color: #54d2ff;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.chatbot-suggestions button:hover {
    background: rgba(84, 210, 255, 0.15);
    border-color: #54d2ff;
}
.chatbot-input-area {
    padding: 12px 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    gap: 10px;
}
.chatbot-input-area input {
    flex: 1;
    padding: 12px 16px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(255, 255, 255, 0.05);
    color: #f4f7fb;
    font-size: 14px;
    outline: none;
}
.chatbot-input-area input:focus {
    border-color: #ff7a18;
}
.chatbot-input-area button {
    padding: 12px 20px;
    border-radius: 999px;
    border: none;
    background: linear-gradient(135deg, #ffb347, #ff7a18);
    color: #111;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s ease;
}
.chatbot-input-area button:hover {
    transform: scale(1.05);
}
</style>

<button class="chatbot-toggle" onclick="toggleChatbot()">💬</button>

<div class="chatbot-window" id="chatbotWindow">
    <div class="chatbot-header">
        <span>🤖</span>
        <div>
            <h3>Zen Fit Assistant</h3>
            <p>Always here to help</p>
        </div>
        <button onclick="toggleChatbot()">✕</button>
    </div>
    <div class="chatbot-messages" id="chatbotMessages">
        <div class="chatbot-message bot">
            Hi! I'm your Zen Fit assistant. I can help you with goals, workouts, diet plans, trainers, progress tracking, and navigating the app. What would you like to know?
            <div class="chatbot-suggestions">
                <button onclick="sendQuickMessage('How do I set a goal?')">Set a goal</button>
                <button onclick="sendQuickMessage('Show me workouts')">Show workouts</button>
                <button onclick="sendQuickMessage('How do I book a trainer?')">Book trainer</button>
            </div>
        </div>
    </div>
    <div class="chatbot-input-area">
        <input type="text" id="chatbotInput" placeholder="Ask me anything..." onkeypress="handleChatbotKey(event)">
        <button onclick="sendChatbotMessage()">Send</button>
    </div>
</div>

<script>
function toggleChatbot() {
    const win = document.getElementById('chatbotWindow');
    win.classList.toggle('open');
}

function handleChatbotKey(e) {
    if (e.key === 'Enter') sendChatbotMessage();
}

function sendQuickMessage(msg) {
    document.getElementById('chatbotInput').value = msg;
    sendChatbotMessage();
}

function sendChatbotMessage() {
    const input = document.getElementById('chatbotInput');
    const msg = input.value.trim();
    if (!msg) return;
    
    addMessage(msg, 'user');
    input.value = '';
    
    setTimeout(() => {
        const response = getBotResponse(msg);
        addMessage(response.text, 'bot');
        if (response.suggestions) {
            addSuggestions(response.suggestions);
        }
    }, 400);
}

function addMessage(text, sender) {
    const container = document.getElementById('chatbotMessages');
    const div = document.createElement('div');
    div.className = 'chatbot-message ' + sender;
    div.innerHTML = text.replace(/\n/g, '<br>');
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function addSuggestions(suggestions) {
    const container = document.getElementById('chatbotMessages');
    const div = document.createElement('div');
    div.className = 'chatbot-message bot';
    div.innerHTML = '<div class="chatbot-suggestions">' + 
        suggestions.map(s => '<button onclick="sendQuickMessage(\'' + s + '\')">' + s + '</button>').join('') +
        '</div>';
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function getBotResponse(message) {
    const ctx = window.chatbotContext || {};
    const msg = message.toLowerCase();
    
    const responses = {
        greeting: {
            patterns: ['hello', 'hi', 'hey', 'help', 'start', 'what can you do'],
            text: "Hi! I'm your Zen Fit assistant. I can help you with goals, workouts, diet plans, trainers, progress tracking, and navigating the app. Just ask!",
            suggestions: ['How do I set a goal?', 'Show me workouts', 'How do I book a trainer?']
        },
        goals: {
            patterns: ['goal', 'set goal', 'fitness goal', 'weight loss', 'weight gain', 'muscle'],
            text: "Your goal shapes your dashboard. Go to Goal Page to select: Weight Loss, Weight Gain, Fitness, Yoga, or Muscle Gain. You can change it anytime!",
            suggestions: ['How do I track progress?', 'What workouts match my goal?']
        },
        workouts: {
            patterns: ['workout', 'exercise', 'training', 'routine', 'gym'],
            text: "The Workout Library has various routines with difficulty levels (Beginner/Intermediate/Advanced), duration, exercises, and video links. Browse and pick sessions matching your goal!",
            suggestions: ['Show me beginner workouts', 'What is the best workout for weight loss?']
        },
        diet: {
            patterns: ['diet', 'nutrition', 'meal', 'food', 'calories'],
            text: "Diet Plans help you eat for your goals with calorie targets and meal schedules. Check the Diet page for plans aligned with your goal!",
            suggestions: ['How many calories for weight loss?', 'Show me meal plans']
        },
        trainers: {
            patterns: ['trainer', 'coach', 'book', 'booking'],
            text: "Book a trainer for personalized coaching! Go to Trainers page to view specialization, experience, ratings, and pricing, then book a session.",
            suggestions: ['How much do trainers cost?', 'Can I cancel a booking?']
        },
        progress: {
            patterns: ['progress', 'track', 'weight', 'log', 'chart'],
            text: "Track your weight and calories over time with visual charts. Log regularly for accurate progress tracking! Visit the Progress page to log entries.",
            suggestions: ['How often should I log?', 'What do the charts show?']
        },
        navigation: {
            patterns: ['how do i', 'where', 'go to', 'navigate', 'page'],
            text: "Use the top navigation: Dashboard (main hub), Goal Page (set focus), Workouts (training), Diet (nutrition), Trainers (booking), Progress (tracking).",
            suggestions: ['Take me to workouts', 'I want to track progress']
        },
        default: {
            text: "I'm here to help! Ask me about goals, workouts, diet, trainers, progress, or how to navigate the app. What would you like to know?",
            suggestions: ['How do I use this app?', 'Show me around']
        }
    };
    
    for (let key in responses) {
        if (key === 'default') continue;
        for (let pattern of responses[key].patterns) {
            if (msg.includes(pattern)) {
                return responses[key];
            }
        }
    }
    return responses.default;
}
</script>