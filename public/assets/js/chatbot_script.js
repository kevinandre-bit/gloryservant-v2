const chatBody = document.querySelector(".chat-body");
const messageInput = document.querySelector(".message-input");
const sendMessage = document.querySelector("#send-message");
const fileInput = document.querySelector("#file-input");
const fileUploadWrapper = document.querySelector(".file-upload-wrapper");
const fileCancelButton = fileUploadWrapper.querySelector("#file-cancel");
const chatbotToggler = document.querySelector("#chatbot-toggler");
const closeChatbot = document.querySelector("#close-chatbot");

// API setup
const API_KEY = "AIzaSyAfth7uQCqktVml9fKkaqlK959cdGYdId4";
const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=${API_KEY}`;

// Initialize user message and file data
const userData = {
  message: null,
  file: {
    data: null,
    mime_type: null,
  },
};

// Store chat history
const chatHistory = [];
const initialInputHeight = messageInput.scrollHeight;

// Auto-scroll check
const shouldAutoScroll = () => {
  const threshold = 100; // px from bottom
  return chatBody.scrollHeight - chatBody.scrollTop - chatBody.clientHeight < threshold;
};

// Save chat history
const saveChatHistory = () => {
  localStorage.setItem("chatHistory", JSON.stringify(chatHistory));
};

// Load chat history
const loadChatHistory = () => {
  const savedHistory = localStorage.getItem("chatHistory");
  if (savedHistory) {
    const parsedHistory = JSON.parse(savedHistory);
    parsedHistory.forEach((entry) => {
      const messageContent = entry.parts.map((part) => {
        if (part.text) return `<div class="message-text">${part.text}</div>`;
        if (part.inline_data) return `<img src="data:${part.inline_data.mime_type};base64,${part.inline_data.data}" class="attachment" />`;
      }).join("");

      const classes = entry.role === "user" ? ["user-message"] : ["bot-message"];
      const messageDiv = createMessageElement(messageContent, ...classes);
      chatBody.appendChild(messageDiv);
    });

    chatHistory.push(...parsedHistory);
    chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });
  }
};

// Create message element
const createMessageElement = (content, ...classes) => {
  const div = document.createElement("div");
  div.classList.add("message", ...classes);
  div.innerHTML = content;
  return div;
};

// Generate bot response
const generateBotResponse = async (incomingMessageDiv) => {
  const messageElement = incomingMessageDiv.querySelector(".message-text");

  chatHistory.push({
    role: "user",
    parts: [{ text: userData.message }, ...(userData.file.data ? [{ inline_data: userData.file }] : [])],
  });
  saveChatHistory();

  const requestOptions = {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      contents: chatHistory,
    }),
  };

  try {
    const response = await fetch(API_URL, requestOptions);
    const data = await response.json();
    if (!response.ok) throw new Error(data.error.message);

    const apiResponseText = data.candidates[0].content.parts[0].text.replace(/\*\*(.*?)\*\*/g, "$1").trim();
    messageElement.innerText = apiResponseText;

    chatHistory.push({
      role: "model",
      parts: [{ text: apiResponseText }],
    });
    saveChatHistory();
  } catch (error) {
    console.log(error);
    messageElement.innerText = error.message;
    messageElement.style.color = "#ff0000";
  } finally {
    userData.file = {};
    incomingMessageDiv.classList.remove("thinking");
    if (shouldAutoScroll()) {
      chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });
    }
  }
};

// Handle outgoing message
const handleOutgoingMessage = (e) => {
  e.preventDefault();
  userData.message = messageInput.value.trim();
  if (!userData.message && !userData.file.data) return;

  messageInput.value = "";
  messageInput.dispatchEvent(new Event("input"));
  fileUploadWrapper.classList.remove("file-uploaded");

  const messageContent = `<div class="message-text"></div>
                          ${userData.file.data ? `<img src="data:${userData.file.mime_type};base64,${userData.file.data}" class="attachment" />` : ""}`;

  const outgoingMessageDiv = createMessageElement(messageContent, "user-message");
  outgoingMessageDiv.querySelector(".message-text").innerText = userData.message;
  chatBody.appendChild(outgoingMessageDiv);

  if (shouldAutoScroll()) {
    chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });
  }

  setTimeout(() => {
    const messageContent = `<img class="bot-avatar" src="https://gloryservant.com/assets/images/faces/default_user5.jpg" width="50" height="50" alt="Bot Avatar">
          <div class="message-text">
            <div class="thinking-indicator">
              <div class="dot"></div>
              <div class="dot"></div>
              <div class="dot"></div>
            </div>
          </div>`;

    const incomingMessageDiv = createMessageElement(messageContent, "bot-message", "thinking");
    chatBody.appendChild(incomingMessageDiv);

    if (shouldAutoScroll()) {
      chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: "smooth" });
    }

    generateBotResponse(incomingMessageDiv);
  }, 600);
};

// Dynamic input resizing
messageInput.addEventListener("input", () => {
  messageInput.style.height = `${initialInputHeight}px`;
  messageInput.style.height = `${messageInput.scrollHeight}px`;
  document.querySelector(".chat-form").style.borderRadius = messageInput.scrollHeight > initialInputHeight ? "15px" : "32px";
});

// Enter key shortcut
messageInput.addEventListener("keydown", (e) => {
  const userMessage = e.target.value.trim();
  if (e.key === "Enter" && !e.shiftKey && userMessage && window.innerWidth > 768) {
    handleOutgoingMessage(e);
  }
});

// Handle file uploads
fileInput.addEventListener("change", () => {
  const file = fileInput.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    fileInput.value = "";
    fileUploadWrapper.querySelector("img").src = e.target.result;
    fileUploadWrapper.classList.add("file-uploaded");
    const base64String = e.target.result.split(",")[1];

    userData.file = {
      data: base64String,
      mime_type: file.type,
    };
  };
  reader.readAsDataURL(file);
});

// Cancel file upload
document.addEventListener("DOMContentLoaded", () => {
  const fileUploadWrapper = document.querySelector(".file-upload-wrapper");

  if (!fileUploadWrapper) {
    console.error("file-upload-wrapper not found!");
  } else {
    console.log("file-upload-wrapper found:", fileUploadWrapper);
    const fileCancelButton = fileUploadWrapper.querySelector("#file-cancel");

    if (fileCancelButton) {
      fileCancelButton.addEventListener("click", () => {
        userData.file = {};
        fileUploadWrapper.classList.remove("file-uploaded");
      });
    } else {
      console.error("File cancel button not found inside the wrapper.");
    }
  }
});

// Emoji picker
const picker = new EmojiMart.Picker({
  theme: "light",
  skinTonePosition: "none",
  previewPosition: "none",
  onEmojiSelect: (emoji) => {
    const { selectionStart: start, selectionEnd: end } = messageInput;
    messageInput.setRangeText(emoji.native, start, end, "end");
    messageInput.focus();
  },
  onClickOutside: (e) => {
    if (e.target.id === "emoji-picker") {
      document.body.classList.toggle("show-emoji-picker");
    } else {
      document.body.classList.remove("show-emoji-picker");
    }
  },
});

document.querySelector(".chat-form").appendChild(picker);

sendMessage.addEventListener("click", (e) => handleOutgoingMessage(e));
document.querySelector("#file-upload").addEventListener("click", () => fileInput.click());
closeChatbot.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));

// Load chat history on page load
loadChatHistory();

// Optional: Clear Chat button
const clearChatBtn = document.querySelector("#clear-chat");
if (clearChatBtn) {
  clearChatBtn.addEventListener("click", () => {
    localStorage.removeItem("chatHistory");
    chatHistory.length = 0;
    chatBody.innerHTML = "";
  });
}