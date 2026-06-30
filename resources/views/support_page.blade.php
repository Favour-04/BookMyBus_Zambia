<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Support | BookMyBus Zambia</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            headline: ['Manrope', 'sans-serif'],
          },
          colors: {
            brand: {
              DEFAULT: '#00601f',
            },
          },
        },
      },
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@400;500;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">

<nav class="fixed inset-x-0 top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur-sm shadow-sm">
  <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
    <a href="{{ url('/') }}" class="text-xl font-extrabold tracking-tight text-brand">🚌 BookMyBus Zambia</a>
    <div class="hidden items-center gap-10 md:flex">
      <a href="{{ route('trips.search') }}" class="text-sm font-semibold text-slate-600 transition hover:text-brand">Search Results</a>
      <a href="#" class="text-sm font-semibold text-slate-600 transition hover:text-brand">My Bookings</a>
      <a href="{{ route('support.page') }}" class="text-sm font-semibold text-brand border-b-2 border-orange-500 pb-1">Support</a>
    </div>
  </div>
</nav>

<main class="mx-auto mt-24 max-w-6xl px-4 pb-20 sm:px-6 lg:px-8">
  <div class="text-center">
    <h1 class="font-headline text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">How can we help you?</h1>
    <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600">We're here to assist you with your travel needs.</p>
  </div>

  <section class="mt-12 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-10">
    <h2 class="text-2xl font-semibold text-slate-900">Frequently Asked Questions</h2>
    <div class="mt-6 space-y-4">
      <div class="divide-y divide-slate-200 overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
        <div class="faq-item px-6 py-5">
          <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-slate-900" onclick="toggleFAQ(this)">
            <span class="text-base font-semibold">❓ How do I book a bus ticket?</span>
            <span class="material-symbols-outlined text-slate-500">expand_more</span>
          </button>
          <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-brand bg-white px-5 py-4 text-sm text-slate-600">Simply search for your route, select a bus, choose your seats, make payment, and you'll receive your digital ticket via email or SMS.</div>
        </div>
        <div class="faq-item px-6 py-5">
          <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-slate-900" onclick="toggleFAQ(this)">
            <span class="text-base font-semibold">❓ What payment methods are accepted?</span>
            <span class="material-symbols-outlined text-slate-500">expand_more</span>
          </button>
          <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-brand bg-white px-5 py-4 text-sm text-slate-600">We accept Airtel Money, MTN Mobile Money, and Zanaco Kwacha. Card payments coming soon.</div>
        </div>
        <div class="faq-item px-6 py-5">
          <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-slate-900" onclick="toggleFAQ(this)">
            <span class="text-base font-semibold">❓ How do I cancel my booking?</span>
            <span class="material-symbols-outlined text-slate-500">expand_more</span>
          </button>
          <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-brand bg-white px-5 py-4 text-sm text-slate-600">Go to "My Bookings", find your trip, and click "Cancel Booking". Cancellation is free if done 24 hours before departure.</div>
        </div>
        <div class="faq-item px-6 py-5">
          <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-slate-900" onclick="toggleFAQ(this)">
            <span class="text-base font-semibold">❓ How do I get my ticket?</span>
            <span class="material-symbols-outlined text-slate-500">expand_more</span>
          </button>
          <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-brand bg-white px-5 py-4 text-sm text-slate-600">After successful payment, you'll receive a digital ticket via email and SMS. You can also view it in "My Bookings".</div>
        </div>
        <div class="faq-item px-6 py-5">
          <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-slate-900" onclick="toggleFAQ(this)">
            <span class="text-base font-semibold">❓ What if I miss my bus?</span>
            <span class="material-symbols-outlined text-slate-500">expand_more</span>
          </button>
          <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-brand bg-white px-5 py-4 text-sm text-slate-600">Please contact customer support immediately. Refunds or rescheduling depend on the bus operator's policy.</div>
        </div>
      </div>
    </div>
  </section>

  <section class="mt-10">
    <h2 class="text-2xl font-semibold text-slate-900">Contact Us</h2>
    <div class="mt-6 grid gap-6 md:grid-cols-3">
      <div class="contact-card rounded-3xl border border-slate-200 bg-white p-6 text-center transition hover:-translate-y-1 hover:shadow-lg">
        <div class="contact-icon mb-4 text-4xl">📞</div>
        <h3 class="mb-2 text-lg font-semibold text-slate-900">Call Us</h3>
        <p class="mb-4 text-sm text-slate-600">Available 24/7 for emergencies</p>
        <p class="text-sm font-semibold text-slate-900">+260 97 1234567</p>
        <p class="text-sm font-semibold text-slate-900">+260 96 7654321</p>
      </div>
      <div class="contact-card rounded-3xl border border-slate-200 bg-white p-6 text-center transition hover:-translate-y-1 hover:shadow-lg">
        <div class="contact-icon mb-4 text-4xl">💬</div>
        <h3 class="mb-2 text-lg font-semibold text-slate-900">WhatsApp</h3>
        <p class="mb-4 text-sm text-slate-600">Quick responses on WhatsApp</p>
        <p class="mb-4 text-sm font-semibold text-slate-900">+260 97 1234567</p>
        <a href="https://wa.me/260971234567" class="contact-btn inline-flex rounded-full bg-brand px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">Chat on WhatsApp →</a>
      </div>
      <div class="contact-card rounded-3xl border border-slate-200 bg-white p-6 text-center transition hover:-translate-y-1 hover:shadow-lg">
        <div class="contact-icon mb-4 text-4xl">✉️</div>
        <h3 class="mb-2 text-lg font-semibold text-slate-900">Email Us</h3>
        <p class="mb-4 text-sm text-slate-600">Send us an email</p>
        <p class="text-sm font-semibold text-slate-900">support@bookmybus.zm</p>
        <p class="mt-2 text-sm text-slate-500">Response within 24 hours</p>
      </div>
    </div>
  </section>

  <section class="mt-10 rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-10">
    <h2 class="text-2xl font-semibold text-slate-900">Send us a message</h2>
    <div class="mt-6 space-y-6">
      <div class="grid gap-6 md:grid-cols-2">
        <div class="form-group">
          <label class="mb-2 block text-sm font-semibold text-slate-700">Your Name</label>
          <input id="name" type="text" placeholder="Enter your full name" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/10" />
        </div>
        <div class="form-group">
          <label class="mb-2 block text-sm font-semibold text-slate-700">Email Address</label>
          <input id="email" type="email" placeholder="Enter your email" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/10" />
        </div>
      </div>
      <div class="form-group">
        <label class="mb-2 block text-sm font-semibold text-slate-700">Subject</label>
        <select id="subject" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/10">
          <option>Booking Issue</option>
          <option>Payment Problem</option>
          <option>Cancellation Request</option>
          <option>General Inquiry</option>
          <option>Feedback</option>
        </select>
      </div>
      <div class="form-group">
        <label class="mb-2 block text-sm font-semibold text-slate-700">Message</label>
        <textarea id="message" rows="4" placeholder="Describe your issue..." class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/10"></textarea>
      </div>
      <button type="button" onclick="sendMessage()" class="w-full rounded-2xl bg-brand px-5 py-3 text-sm font-semibold text-white transition hover:bg-green-700">Send Message</button>
    </div>
  </section>
</main>

<footer class="border-t border-slate-200 bg-slate-50 py-10">
  <div class="mx-auto max-w-7xl px-4 text-center text-sm text-slate-500 sm:px-6 lg:px-8">
    <p class="font-semibold text-slate-900">🚌 BookMyBus Zambia</p>
    <p class="mt-2">© 2025 BookMyBus Zambia. Premium Travel Excellence.</p>
    <div class="mt-4 flex flex-wrap justify-center gap-4">
      <a href="#" class="text-slate-500 transition hover:text-slate-900">Privacy Policy</a>
      <a href="#" class="text-slate-500 transition hover:text-slate-900">Terms of Service</a>
      <a href="#" class="text-slate-500 transition hover:text-slate-900">Carrier Partners</a>
      <a href="#" class="text-slate-500 transition hover:text-slate-900">Contact Us</a>
    </div>
  </div>
</footer>

<div id="toast" class="fixed bottom-8 left-1/2 z-50 -translate-x-1/2 rounded-full bg-slate-900 px-4 py-2 text-sm text-white opacity-0 transition-opacity"></div>

<script>
  function toggleFAQ(element) {
    const answer = element.parentElement.querySelector('.faq-answer');
    const icon = element.querySelector('.material-symbols-outlined');
    answer.classList.toggle('hidden');
    answer.classList.toggle('show');
    if (answer.classList.contains('show')) {
      icon.innerText = 'expand_less';
    } else {
      icon.innerText = 'expand_more';
    }
  }

  function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.classList.add('opacity-100');
    toast.classList.remove('opacity-0');
    setTimeout(() => {
      toast.classList.add('opacity-0');
      toast.classList.remove('opacity-100');
    }, 3000);
  }

  function sendMessage() {
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;

    if (!name || !email || !message) {
      showToast('Please fill in all fields');
      return;
    }

    showToast('✅ Message sent! We will respond within 24 hours.');
</html>
