document.addEventListener('DOMContentLoaded', () => {
  const menuToggle = document.querySelector('.menu-toggle');
  const sidebar = document.querySelector('.app-sidebar');
  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', () => {
      const isOpen = sidebar.classList.toggle('open');
      menuToggle.setAttribute('aria-expanded', String(isOpen));
    });

    sidebar.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        sidebar.classList.remove('open');
        menuToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  const userButton = document.querySelector('.topbar-user-button');
  const userMenu = document.querySelector('.user-menu');
  if (userButton && userMenu) {
    userButton.addEventListener('click', event => {
      event.stopPropagation();
      const isOpen = userMenu.classList.toggle('open');
      userButton.setAttribute('aria-expanded', String(isOpen));
    });

    userMenu.addEventListener('click', event => event.stopPropagation());

    document.addEventListener('click', () => {
      userMenu.classList.remove('open');
      userButton.setAttribute('aria-expanded', 'false');
    });
  }

  const usernameInput = document.getElementById('username');
  const passwordInput = document.getElementById('password');
  const showToggle = document.getElementById('show');
  const remember = document.getElementById('checkbox');

  if (usernameInput && remember) {
    const rememberedUser = localStorage.getItem('scapms_username');
    const rememberFlag = localStorage.getItem('scapms_remembered') === '1';

    if (rememberFlag && rememberedUser) {
      usernameInput.value = rememberedUser;
      remember.checked = true;
    }

    remember.addEventListener('change', () => {
      if (remember.checked && usernameInput.value.trim()) {
        localStorage.setItem('scapms_username', usernameInput.value.trim());
        localStorage.setItem('scapms_remembered', '1');
      } else {
        localStorage.removeItem('scapms_username');
        localStorage.setItem('scapms_remembered', '0');
      }
    });

    usernameInput.addEventListener('input', () => {
      if (remember.checked && usernameInput.value.trim()) {
        localStorage.setItem('scapms_username', usernameInput.value.trim());
      }
    });
  }

  if (passwordInput && showToggle) {
    showToggle.addEventListener('click', () => {
      const isHidden = passwordInput.type === 'password';
      passwordInput.type = isHidden ? 'text' : 'password';
      showToggle.alt = isHidden ? 'Hide password' : 'Show password';
      showToggle.title = isHidden ? 'Hide password' : 'Show password';
      showToggle.src = isHidden ? 'icons/hiddenicon.png' : 'icons/showicon.png';
      showToggle.style.opacity = isHidden ? '0.8' : '1';
    });
  }
});

function validate() {
  if (document.getElementById('password')) {
    return true;
  }
  return true;
}

// STUDENT DASHBOARD
const courses = [
  {
    id: "cs",
    title: "Introduction to computer science",
    instructor: "Dr. John K.",
    lessons: [
      "What is Computer Science?",
      "History of Computers",
      "Computer Hardware Basics",
      "Operating Systems",
      "Algorithms & Problem Solving",
      "Introduction to Programming",
      "Data Representation",
      "Computer Networks",
      "Cybersecurity Basics",
      "Final Course Review"
    ]
  },
  {
    id: "web",
    title: "Web Development",
    instructor: "Mr. Ona",
    lessons: [
      "HTML Fundamentals",
      "Semantic HTML",
      "CSS Fundamentals",
      "Responsive Design",
      "JavaScript Basics",
      "DOM Manipulation",
      "Events & Forms",
      "Local Storage",
      "Building a Complete Website",
      "Web Development Review"
    ]
  },
  {
    id: "db",
    title: "Database Systems",
    instructor: "Dr. Samuel",
    lessons: [
      "Database Concepts",
      "Relational Databases",
      "Tables & Relationships",
      "Primary & Foreign Keys",
      "SQL SELECT",
      "INSERT, UPDATE & DELETE",
      "JOINs",
      "Normalization",
      "Database Security",
      "Final Database Review"
    ]
  }
];

const STORAGE_KEY = "scapms_progress_v1";
let progress = JSON.parse(localStorage.getItem(STORAGE_KEY) || "{}");

function ensureProgress() {
  courses.forEach(course => {
    if (!Array.isArray(progress[course.id])) {
      progress[course.id] = course.lessons.map(() => false);
    }
    if (progress[course.id].length !== course.lessons.length) {
      progress[course.id] = course.lessons.map((_, i) => Boolean(progress[course.id][i]));
    }
  });
  save();
}

function save() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(progress));
}

function getPercent(course) {
  const done = progress[course.id].filter(Boolean).length;
  return Math.round((done / course.lessons.length) * 100);
}

function renderCourses() {
  const list = document.getElementById("courseList");
  list.innerHTML = courses.map(course => {
    const percent = getPercent(course);
    return `
      <article class="course-card">
        <div class="course-info">
          <h2 class="course-title">
          ${course.title}
          </h2>
          <div class="instructor">Instructor:
           ${course.instructor}
           </div>
          <div class="progress-label">Progress: 
          ${percent}%
          </div>
          <div class="progress-track" 
          aria-label="${percent}% complete">
            <div class="progress-fill" style="width:${percent}%"></div>
          </div>
        </div>
        <button class="view-details" 
        data-course="${course.id}">View Details
        </button>
      </article>
    `;
  }).join("");

  document.querySelectorAll(".view-details").forEach(btn => {
    btn.addEventListener("click", () => openModal(btn.dataset.course));
  });

  updateDashboard();
}

function openModal(courseId) {
  const course = courses.find(c => c.id === courseId);
  const overlay = document.getElementById("modalOverlay");

  document.getElementById("modalTitle").textContent = course.title;
  document.getElementById("modalInstructor").textContent = `Instructor: ${course.instructor}`;

  const renderModal = () => {
    const done = progress[course.id].filter(Boolean).length;
    const percent = getPercent(course);

    document.getElementById("modalPercent").textContent = `${percent}%`;
    document.getElementById("modalProgressBar").style.width = `${percent}%`;
    document.getElementById("lessonCounter").textContent =
      `${done} / ${course.lessons.length}`;

    document.getElementById("lessonList").innerHTML = course.lessons.map((lesson, index) => {
      const completed = progress[course.id][index];
      return `
        <div class="lesson ${completed ? "completed" : ""}">
          <button class="lesson-check" data-index="${index}" aria-label="Toggle lesson">
            ${completed ? "✓" : ""}
          </button>
          <span class="lesson-name">${index + 1}. ${lesson}</span>
          <button class="complete-btn" data-index="${index}">
            ${completed ? "Completed" : "Mark Complete"}
          </button>
        </div>
      `;
    }).join("");

    document.querySelectorAll(".lesson-check, .complete-btn").forEach(button => {
      button.addEventListener("click", () => {
        const index = Number(button.dataset.index);
        progress[course.id][index] = !progress[course.id][index];
        save();
        renderCourses();
        renderModal();
      });
    });
  };

  renderModal();
  overlay.classList.remove("hidden");
  document.body.style.overflow = "hidden";
}

function closeModal() {
  document.getElementById("modalOverlay").classList.add("hidden");
  document.body.style.overflow = "";
}

function updateDashboard() {
  const completed = courses.reduce(
    (sum, course) => sum + progress[course.id].filter(Boolean).length, 0
  );
  const total = courses.reduce((sum, course) => sum + course.lessons.length, 0);
  const percent = total ? Math.round((completed / total) * 100) : 0;

  document.getElementById("completedLessons").textContent = completed;
  document.getElementById("overallProgress").textContent = `${percent}%`;
}

function showPage(page) {
  const coursesPage = document.getElementById("coursesPage");
  const dashboardPage = document.getElementById("dashboardPage");
  const otherPage = document.getElementById("otherPage");

  coursesPage.classList.add("hidden");
  dashboardPage.classList.add("hidden");
  otherPage.classList.add("hidden");

  if (page === "courses") {
    coursesPage.classList.remove("hidden");
  } else if (page === "dashboard") {
    dashboardPage.classList.remove("hidden");
  } else {
    otherPage.classList.remove("hidden");
    const titles = {
      assessments: ["Assessments", "Assessments Coming Soon"],
      results: ["Results & feedback", "Results & Feedback Coming Soon"],
      history: ["View History", "History Coming Soon"]
    };
    document.getElementById("otherTitle").textContent = titles[page][0];
    document.getElementById("otherHeading").textContent = titles[page][1];
  }

  document.querySelectorAll(".nav-item").forEach(item => {
    item.classList.toggle("active", item.dataset.page === page);
  });

  document.getElementById("sidebar").classList.remove("open");
}

document.getElementById("menuBtn").addEventListener("click", () => {
  const sidebar = document.getElementById("sidebar");
  const content = document.querySelector(".content");

  // Mobile: slide the sidebar in/out.
  if (window.innerWidth <= 720) {
    sidebar.classList.toggle("open");
    return;
  }

  // Desktop/tablet: hide/show the sidebar and let content expand.
  const collapsed = sidebar.classList.toggle("collapsed");
  content.classList.toggle("sidebar-collapsed", collapsed);
});

window.addEventListener("resize", () => {
  const sidebar = document.getElementById("sidebar");
  const content = document.querySelector(".content");

  if (window.innerWidth <= 720) {
    sidebar.classList.remove("collapsed");
    content.classList.remove("sidebar-collapsed");
  } else {
    sidebar.classList.remove("open");
  }
});

document.getElementById("closeModal").addEventListener("click", closeModal);
document.getElementById("modalOverlay").addEventListener("click", e => {
  if (e.target.id === "modalOverlay") closeModal();
});

document.addEventListener("keydown", e => {
  if (e.key === "Escape") closeModal();
});

document.querySelectorAll(".nav-item").forEach(item => {
  item.addEventListener("click", () => showPage(item.dataset.page));
});

ensureProgress();
renderCourses();

// User profile modal
const profileOverlay = document.getElementById("profileOverlay");
const profileBtn = document.getElementById("profileBtn");
const closeProfile = document.getElementById("closeProfile");
const closeProfileAction = document.getElementById("closeProfileAction");

function openProfile() {
  profileOverlay.classList.remove("hidden");
  document.body.style.overflow = "hidden";
}

function closeProfileModal() {
  profileOverlay.classList.add("hidden");
  if (document.getElementById("modalOverlay").classList.contains("hidden")) {
    document.body.style.overflow = "";
  }
}

profileBtn.addEventListener("click", openProfile);
closeProfile.addEventListener("click", closeProfileModal);
closeProfileAction.addEventListener("click", closeProfileModal);
profileOverlay.addEventListener("click", e => {
  if (e.target === profileOverlay) closeProfileModal();
});

document.addEventListener("keydown", e => {
  if (e.key === "Escape" && !profileOverlay.classList.contains("hidden")) {
    closeProfileModal();
  }
});