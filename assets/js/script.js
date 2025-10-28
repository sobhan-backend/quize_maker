// script.js — تعاملات فرانت‌اند
document.addEventListener('DOMContentLoaded', ()=>{
  // گزینه‌ها را طوری می‌کنیم که کلیک روی بلوک، رادیو را انتخاب کند
  document.querySelectorAll('.option-card').forEach(card => {
    card.addEventListener('click', (e) => {
      const radio = card.querySelector('input[type="radio"]');
      if (radio && !radio.disabled) {
        radio.checked = true;
        // افزودن کلاس selected
        const siblings = card.parentElement.querySelectorAll('.option-card');
        siblings.forEach(s=>s.classList.remove('selected'));
        card.classList.add('selected');
      }
    });
    // اگر کاربر مستقیم روی رادیو کلیک کند نیز کلاس را تنظیم کن
    const radio = card.querySelector('input[type="radio"]');
    if (radio){
      radio.addEventListener('change', ()=>{
        const siblings = card.parentElement.querySelectorAll('.option-card');
        siblings.forEach(s=>s.classList.remove('selected'));
        if (radio.checked) card.classList.add('selected');
      });
    }
  });

  // در صورت وجود فرم سوالات، هنگام submit یک loader ساده نشان بده
  const answerForm = document.getElementById('answerForm');
  if (answerForm){
    answerForm.addEventListener('submit', ()=>{
      const btns = answerForm.querySelectorAll('button[type="submit"]');
      btns.forEach(b=>{ b.disabled = true; b.innerHTML = 'در حال پردازش...'; });
    });
  }

  // شمارنده سوال‌ها: فعال‌سازی کلاس برای سوال جاری
  document.querySelectorAll('.q-pagination .badge').forEach(b=>{
    b.addEventListener('click', (e)=>{
      e.preventDefault();
      const idx = b.dataset.index;
      const form = document.getElementById('answerForm');
      if (!form) return;
      const input = document.createElement('input');
      input.type = 'hidden'; input.name = 'goto'; input.value = idx;
      form.appendChild(input);
      form.submit();
    });
  });
});