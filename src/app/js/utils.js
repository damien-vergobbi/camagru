const email_regex = /^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/;

// Username at least 3 characters, alphanumeric, underscores and hyphens with at least 1 letter
const username_regex = /^(?=.*[a-z])[a-z0-9_-]{3,30}$/i;

// Password at least 6 characters, 1 uppercase, 1 lowercase, 1 number
const password_regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{6,30}$/;

// Comment regex : Letters (maj, min and accents), numbers, spaces, and punctuation
const comment_regex = /^[a-zA-Z0-9\s.,;:!?'-éèàêâîôûùç]{3,150}$/;
