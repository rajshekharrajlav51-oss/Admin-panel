import { initializeApp } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/11.0.1/firebase-messaging.js";

async function initFirebase() {
    try {
        let firebaseConfig = window.__firebaseConfig || null;

        if (!firebaseConfig || Object.keys(firebaseConfig).length === 0) {
            const { data } = await axios.get("/api/settings/firebase-config");
            firebaseConfig = data.data;
        }

        localStorage.setItem("firebase_config", JSON.stringify(firebaseConfig));

        const requiredKeys = ["apiKey", "authDomain", "appId", "projectId", "messagingSenderId"];
        const hasRequiredConfig = requiredKeys.every((key) => firebaseConfig && firebaseConfig[key]);

        if (!hasRequiredConfig) {
            console.info("Firebase init skipped: incomplete Firebase configuration.", window.__firebaseConfigStatus || {});
            return;
        }

        if (!firebaseConfig.vapidKey) {
            console.info("Firebase messaging skipped: missing VAPID key.");
            return;
        }

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        const permission = await Notification.requestPermission();
        if (permission !== "granted") {
            console.warn("Notification permission not granted");
            return;
        }

        const token = await getToken(messaging, { vapidKey: firebaseConfig.vapidKey });
        localStorage.setItem("fcm_token", token);

        onMessage(messaging, (payload) => {
            console.log("Message received in foreground:", payload);

            const { title, body, image } = payload.notification || {};

            let toastContainer = document.getElementById("toastContainer");
            if (!toastContainer) {
                toastContainer = document.createElement("div");
                toastContainer.id = "toastContainer";
                toastContainer.className = "toast-container position-fixed top-0 end-0 p-3";
                document.body.appendChild(toastContainer);
            }

            const toastEl = document.createElement("div");
            toastEl.className = "toast align-items-center text-bg-blue border-0 show mb-2 shadow";
            toastEl.setAttribute("role", "alert");
            toastEl.setAttribute("aria-live", "assertive");
            toastEl.setAttribute("aria-atomic", "true");

            toastEl.innerHTML = `
        <div class="toast-header">
            ${image ? `<img src="${image}" class="rounded me-2" alt="Notification Image" style="width:30px;height:30px;object-fit:cover;">` : ""}
            <strong class="me-auto">${title || "Notification"}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${body || ""}
        </div>
    `;

            toastContainer.appendChild(toastEl);

            try {
                const audio = new Audio("/assets/sound/notification.wav");
                audio.volume = 1.0;
                audio.play().catch((err) => {
                    console.warn("Autoplay blocked for notification sound:", err);
                });
            } catch (e) {
                console.warn("Failed to play notification sound:", e);
            }
        });
    } catch (err) {
        console.error("Error initializing Firebase:", err);
    }
}

initFirebase();
