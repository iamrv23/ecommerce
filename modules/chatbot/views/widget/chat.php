<?php

/* @var $this yii\web\View */

$this->title = 'Chatbot Widget';
?>

<div id="chatbot-widget" class="chatbot-widget">
    <div class="chatbot-header">
        <h4>AI Assistant</h4>
        <button id="chatbot-toggle" class="btn btn-sm btn-secondary">Minimize</button>
    </div>

    <div class="chatbot-messages" id="chatbot-messages">
        <div class="message bot-message">
            <div class="message-content">
                Hello! I'm your AI assistant. How can I help you today?
            </div>
        </div>
    </div>

    <div class="chatbot-input">
        <div class="input-group">
            <input type="text" id="chatbot-input-field" class="form-control"
                   placeholder="Type your message..." maxlength="500">
            <button id="chatbot-send" class="btn btn-primary">Send</button>
        </div>
        <div class="typing-indicator" id="typing-indicator" style="display: none;">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    height: 500px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    display: flex;
    flex-direction: column;
    font-family: Arial, sans-serif;
}

.chatbot-header {
    background: #007bff;
    color: white;
    padding: 10px 15px;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8f9fa;
}

.message {
    margin-bottom: 10px;
    display: flex;
}

.message.user-message {
    justify-content: flex-end;
}

.message.bot-message {
    justify-content: flex-start;
}

.message-content {
    max-width: 80%;
    padding: 8px 12px;
    border-radius: 18px;
    word-wrap: break-word;
}

.user-message .message-content {
    background: #007bff;
    color: white;
}

.bot-message .message-content {
    background: white;
    color: #333;
    border: 1px solid #e9ecef;
}

.chatbot-input {
    padding: 15px;
    border-top: 1px solid #e9ecef;
    background: white;
    border-radius: 0 0 8px 8px;
}

.typing-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px;
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #007bff;
    margin: 0 2px;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}

@media (max-width: 480px) {
    .chatbot-widget {
        width: calc(100vw - 40px);
        height: calc(100vh - 40px);
        bottom: 20px;
        right: 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const widget = document.getElementById('chatbot-widget');
    const messages = document.getElementById('chatbot-messages');
    const input = document.getElementById('chatbot-input-field');
    const sendBtn = document.getElementById('chatbot-send');
    const toggleBtn = document.getElementById('chatbot-toggle');
    const typingIndicator = document.getElementById('typing-indicator');

    let sessionId = localStorage.getItem('chatbot_session_id') || generateSessionId();
    localStorage.setItem('chatbot_session_id', sessionId);

    function generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    function addMessage(content, isUser = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message ' + (isUser ? 'user-message' : 'bot-message');

        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.textContent = content;

        messageDiv.appendChild(contentDiv);
        messages.appendChild(messageDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    function showTyping() {
        typingIndicator.style.display = 'flex';
    }

    function hideTyping() {
        typingIndicator.style.display = 'none';
    }

    async function sendMessage(message) {
        addMessage(message, true);
        showTyping();
        input.value = '';

        try {
            const response = await fetch('/chatbot/api/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                },
                body: new URLSearchParams({
                    message: message,
                    session_id: sessionId
                })
            });

            const data = await response.json();

            if (data.success) {
                addMessage(data.message);
                sessionId = data.session_id;
                localStorage.setItem('chatbot_session_id', sessionId);
            } else {
                addMessage('Sorry, there was an error: ' + data.message);
            }
        } catch (error) {
            console.error('Chatbot error:', error);
            addMessage('Sorry, I\'m having trouble connecting right now. Please try again later.');
        } finally {
            hideTyping();
        }
    }

    sendBtn.addEventListener('click', function() {
        const message = input.value.trim();
        if (message) {
            sendMessage(message);
        }
    });

    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const message = input.value.trim();
            if (message) {
                sendMessage(message);
            }
        }
    });

    toggleBtn.addEventListener('click', function() {
        const isMinimized = widget.style.height === '50px';

        if (isMinimized) {
            widget.style.height = '500px';
            messages.style.display = 'block';
            document.querySelector('.chatbot-input').style.display = 'block';
            toggleBtn.textContent = 'Minimize';
        } else {
            widget.style.height = '50px';
            messages.style.display = 'none';
            document.querySelector('.chatbot-input').style.display = 'none';
            toggleBtn.textContent = 'Maximize';
        }
    });
});
</script>