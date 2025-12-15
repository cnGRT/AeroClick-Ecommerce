// MIT License - Simple Star Rating Widget
document.querySelectorAll('.star-rating').forEach(rating => {
    const stars = rating.querySelectorAll('.star');
    let currentRating = 0;

    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            currentRating = index + 1;
            stars.forEach((s, i) => {
                s.classList.toggle('active', i < currentRating);
            });
            // 将评分值写入隐藏 input（供 PHP 处理）
            const input = rating.nextElementSibling;
            if (input && input.type === 'hidden') {
                input.value = currentRating;
            }
        });

        star.addEventListener('mouseover', () => {
            stars.forEach((s, i) => {
                s.classList.toggle('hover', i <= index);
            });
        });

        star.addEventListener('mouseout', () => {
            stars.forEach(s => s.classList.remove('hover'));
            stars.forEach((s, i) => s.classList.toggle('active', i < currentRating));
        });
    });
});