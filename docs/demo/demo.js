/*
 * Glovia – static demo helper (GitHub Pages)
 * The real site runs on PHP + MySQL. This static copy keeps everything that can work
 * without a server (browsing, product pages, the skin quiz) and explains the rest.
 */
(function () {
  var REPO = 'https://github.com/zainabthaa/glovia';

  /* ---------- small toast message ---------- */
  function notice(text) {
    var t = document.getElementById('demo-toast');
    if (!t) {
      t = document.createElement('div');
      t.id = 'demo-toast';
      t.setAttribute('role', 'status');
      document.body.appendChild(t);
    }
    t.textContent = text;
    t.classList.add('show');
    clearTimeout(t._timer);
    t._timer = setTimeout(function () { t.classList.remove('show'); }, 3800);
  }

  /* ---------- skin quiz: same scoring as buildRoutine.php ---------- */
  function quizResult(form) {
    var order = ['Dryness', 'Redness', 'Oily', 'Combo', 'Normal', 'Scarring'];
    var scores = {};
    order.forEach(function (k) { scores[k] = 0; });
    form.querySelectorAll('input[type=radio]:checked').forEach(function (r) {
      if (scores.hasOwnProperty(r.value)) scores[r.value]++;
    });
    // PHP's arsort() keeps the original order for ties, so the first highest score wins
    var best = order[0];
    order.forEach(function (k) { if (scores[k] > scores[best]) best = k; });
    return best;
  }

  /* ---------- forms: quiz works, everything else explains the demo ---------- */
  document.addEventListener('submit', function (e) {
    var form = e.target;
    e.preventDefault();
    e.stopImmediatePropagation();
    if (form.querySelector('input[name^="answers["]')) {
      window.location.href = 'quiz-' + quizResult(form) + '.html';
      return;
    }
    notice('This is a static demo. Accounts, reviews, the contact form and the admin panel need the PHP + MySQL backend. See the code on GitHub.');
  }, true);

  /* ---------- like buttons ---------- */
  document.addEventListener('click', function (e) {
    var heart = e.target.closest && e.target.closest('.btn-heart');
    if (!heart) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    notice('Saving favorites needs an account, which is available in the full PHP version.');
  }, true);

  /* ---------- banner ---------- */
  document.addEventListener('DOMContentLoaded', function () {
    try { if (sessionStorage.getItem('demoBannerClosed')) return; } catch (err) { /* ignore */ }
    var b = document.createElement('div');
    b.id = 'demo-banner';
    b.innerHTML =
      '<span><strong>Static demo</strong> – browsing and the skin quiz work here; sign-in, likes, reviews and admin need the PHP backend.</span>' +
      '<a href="' + REPO + '" target="_blank" rel="noopener">View code &amp; security report</a>' +
      '<button type="button" aria-label="Close">&times;</button>';
    b.querySelector('button').addEventListener('click', function () {
      b.remove();
      try { sessionStorage.setItem('demoBannerClosed', '1'); } catch (err) { /* ignore */ }
    });
    document.body.appendChild(b);
  });
})();
