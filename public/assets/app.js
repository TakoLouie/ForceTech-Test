const form = document.querySelector('#shorten-form');
const input = document.querySelector('#url');
const message = document.querySelector('#message');
const result = document.querySelector('#result');
const shortUrl = document.querySelector('#short-url');
const copyButton = document.querySelector('#copy');

function showMessage(text, isError = false) {
  message.textContent = text;
  message.classList.toggle('error', isError);
}

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  const url = input.value.trim();
  result.hidden = true;

  if (!url) {
    showMessage('Please enter a URL.', true);
    input.focus();
    return;
  }

  const button = form.querySelector('button');
  button.disabled = true;
  button.textContent = 'Shortening…';
  showMessage('');

  try {
    const response = await fetch('/api/shorten', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ url })
    });
    const data = await response.json();
    if (!response.ok) throw new Error(data.error || 'Something went wrong.');

    shortUrl.href = data.shortUrl;
    shortUrl.textContent = data.shortUrl;
    result.hidden = false;
    showMessage('Your link is ready.');
  } catch (error) {
    showMessage(error.message, true);
  } finally {
    button.disabled = false;
    button.textContent = 'Shorten';
  }
});

copyButton.addEventListener('click', async () => {
  try {
    await navigator.clipboard.writeText(shortUrl.href);
    copyButton.textContent = 'Copied!';
    setTimeout(() => { copyButton.textContent = 'Copy'; }, 1500);
  } catch {
    showMessage('Copy failed. Select the link and copy it manually.', true);
  }
});
