@extends('layouts.app')

@section('title', 'Support | BookMyBus Zambia')
@section('main-class', 'pb-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8')

@section('content')
<div class="text-center">
  <h1 class="font-headline text-4xl font-extrabold tracking-tight text-on-surface sm:text-5xl">How can we help you?</h1>
  <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-on-surface-variant">We're here to assist you with your travel needs.</p>
</div>

<section class="mt-12 rounded-[2rem] bg-surface-container-lowest p-6 shadow-sm ring-1 ring-outline-variant/15 sm:p-10">
  <h2 class="text-2xl font-semibold text-on-surface">Frequently Asked Questions</h2>
  <div class="mt-6 space-y-4">
    <div class="divide-y divide-outline-variant/15 overflow-hidden rounded-3xl border border-outline-variant/15 bg-surface-container-low">
      <div class="faq-item px-6 py-5">
        <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-on-surface" onclick="toggleFAQ(this)">
          <span class="text-base font-semibold">
            <span class="material-symbols-outlined text-primary align-middle mr-1">help</span>
            How do I book a bus ticket?
          </span>
          <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
        </button>
        <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-primary bg-surface-container-lowest px-5 py-4 text-sm text-on-surface-variant">Simply search for your route, select a bus, choose your seats, make payment, and you'll receive your digital ticket via email or SMS.</div>
      </div>
      <div class="faq-item px-6 py-5">
        <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-on-surface" onclick="toggleFAQ(this)">
          <span class="text-base font-semibold">
            <span class="material-symbols-outlined text-primary align-middle mr-1">help</span>
            What payment methods are accepted?
          </span>
          <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
        </button>
        <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-primary bg-surface-container-lowest px-5 py-4 text-sm text-on-surface-variant">We accept Airtel Money, MTN Mobile Money, and Zanaco Kwacha. Card payments coming soon.</div>
      </div>
      <div class="faq-item px-6 py-5">
        <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-on-surface" onclick="toggleFAQ(this)">
          <span class="text-base font-semibold">
            <span class="material-symbols-outlined text-primary align-middle mr-1">help</span>
            How do I cancel my booking?
          </span>
          <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
        </button>
        <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-primary bg-surface-container-lowest px-5 py-4 text-sm text-on-surface-variant">Go to "My Bookings", find your trip, and click "Cancel Booking". Cancellation is free if done 24 hours before departure.</div>
      </div>
      <div class="faq-item px-6 py-5">
        <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-on-surface" onclick="toggleFAQ(this)">
          <span class="text-base font-semibold">
            <span class="material-symbols-outlined text-primary align-middle mr-1">help</span>
            How do I get my ticket?
          </span>
          <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
        </button>
        <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-primary bg-surface-container-lowest px-5 py-4 text-sm text-on-surface-variant">After successful payment, you'll receive a digital ticket via email and SMS. You can also view it in "My Bookings".</div>
      </div>
      <div class="faq-item px-6 py-5">
        <button type="button" class="faq-question flex w-full items-center justify-between gap-4 text-left text-on-surface" onclick="toggleFAQ(this)">
          <span class="text-base font-semibold">
            <span class="material-symbols-outlined text-primary align-middle mr-1">help</span>
            What if I miss my bus?
          </span>
          <span class="material-symbols-outlined text-on-surface-variant">expand_more</span>
        </button>
        <div class="faq-answer mt-4 hidden rounded-b-2xl border-l-4 border-primary bg-surface-container-lowest px-5 py-4 text-sm text-on-surface-variant">Please contact customer support immediately. Refunds or rescheduling depend on the bus operator's policy.</div>
      </div>
    </div>
  </div>
</section>

<section class="mt-10">
  <h2 class="text-2xl font-semibold text-on-surface">Contact Us</h2>
  <div class="mt-6 grid gap-6 md:grid-cols-3">
    <div class="contact-card rounded-3xl border border-outline-variant/15 bg-surface-container-lowest p-6 text-center transition hover:-translate-y-1 hover:shadow-lg">
      <div class="contact-icon mb-4 text-primary">
        <span class="material-symbols-outlined text-4xl">call</span>
      </div>
      <h3 class="mb-2 text-lg font-semibold text-on-surface">Call Us</h3>
      <p class="mb-4 text-sm text-on-surface-variant">Available 24/7 for emergencies</p>
      <p class="text-sm font-semibold text-on-surface">+260 97 1234567</p>
      <p class="text-sm font-semibold text-on-surface">+260 96 7654321</p>
    </div>
    <div class="contact-card rounded-3xl border border-outline-variant/15 bg-surface-container-lowest p-6 text-center transition hover:-translate-y-1 hover:shadow-lg">
      <div class="contact-icon mb-4 text-primary">
        <span class="material-symbols-outlined text-4xl">chat</span>
      </div>
      <h3 class="mb-2 text-lg font-semibold text-on-surface">WhatsApp</h3>
      <p class="mb-4 text-sm text-on-surface-variant">Quick responses on WhatsApp</p>
      <p class="mb-4 text-sm font-semibold text-on-surface">+260 97 1234567</p>
      <a href="https://wa.me/260971234567" class="contact-btn inline-flex rounded-full bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition hover:brightness-110">Chat on WhatsApp →</a>
    </div>
    <div class="contact-card rounded-3xl border border-outline-variant/15 bg-surface-container-lowest p-6 text-center transition hover:-translate-y-1 hover:shadow-lg">
      <div class="contact-icon mb-4 text-primary">
        <span class="material-symbols-outlined text-4xl">mail</span>
      </div>
      <h3 class="mb-2 text-lg font-semibold text-on-surface">Email Us</h3>
      <p class="mb-4 text-sm text-on-surface-variant">Send us an email</p>
      <p class="text-sm font-semibold text-on-surface">support@bookmybus.co.zm</p>
      <p class="mt-2 text-sm text-on-surface-variant">Response within 24 hours</p>
    </div>
  </div>
</section>

<section class="mt-10 rounded-[2rem] bg-surface-container-lowest p-6 shadow-sm ring-1 ring-outline-variant/15 sm:p-10">
  <h2 class="text-2xl font-semibold text-on-surface">Send us a message</h2>
  <div class="mt-6 space-y-6">
    <div class="grid gap-6 md:grid-cols-2">
      <div class="form-group">
        <label class="mb-2 block text-sm font-semibold text-on-surface">Your Name</label>
        <input id="name" type="text" placeholder="Enter your full name" class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10" />
      </div>
      <div class="form-group">
        <label class="mb-2 block text-sm font-semibold text-on-surface">Email Address</label>
        <input id="email" type="email" placeholder="Enter your email" class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10" />
      </div>
    </div>
    <div class="form-group">
      <label class="mb-2 block text-sm font-semibold text-on-surface">Subject</label>
      <select id="subject" class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10">
        <option>Booking Issue</option>
        <option>Payment Problem</option>
        <option>Cancellation Request</option>
        <option>General Inquiry</option>
        <option>Feedback</option>
      </select>
    </div>
    <div class="form-group">
      <label class="mb-2 block text-sm font-semibold text-on-surface">Message</label>
      <textarea id="message" rows="4" placeholder="Describe your issue..." class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-sm text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10"></textarea>
    </div>
    <button type="button" onclick="sendMessage()" class="w-full rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-on-primary transition hover:brightness-110">Send Message</button>
  </div>
</section>
@endsection

@push('scripts')
<script>
  function toggleFAQ(element) {
    const answer = element.parentElement.querySelector('.faq-answer');
    const icon = element.querySelector('.material-symbols-outlined:last-child');
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
  }
</script>
@endpush