// Элементы DOM
const fruitCards = document.querySelectorAll('.fruit-card');
const fruitDisplay = document.getElementById('fruitDisplay');
const fruitWrapper = document.getElementById('fruitWrapper');
const fruitSkin = document.getElementById('fruitSkin');
const fruitFlesh = document.getElementById('fruitFlesh');
const peelButton = document.getElementById('peelButton');
const resetButton = document.getElementById('resetButton');
const peelPieces = document.getElementById('peelPieces');

let selectedFruit = null;
let isPeeled = false;

// Обработчик выбора фрукта
fruitCards.forEach(card => {
    card.addEventListener('click', () => {
        if (isPeeled) return;
        
        // Убираем выделение с других карточек
        fruitCards.forEach(c => c.classList.remove('selected'));
        
        // Выделяем выбранную карточку
        card.classList.add('selected');
        
        // Получаем тип фрукта
        selectedFruit = card.dataset.fruit;
        
        // Показываем фрукт
        showFruit(selectedFruit);
        
        // Фрукт готов к очистке - можно кликать на него
    });
});

// Функция показа фрукта
function showFruit(fruitType) {
    // Сбрасываем состояние
    fruitSkin.classList.remove('peeling');
    fruitFlesh.classList.remove('revealed');
    fruitSkin.style.opacity = '1';
    fruitSkin.style.visibility = 'visible';
    fruitSkin.style.transform = '';
    fruitFlesh.style.opacity = '0';
    fruitFlesh.style.visibility = 'visible';
    peelPieces.innerHTML = '';
    isPeeled = false;
    
    // Устанавливаем классы для стилизации
    fruitSkin.className = 'fruit-skin ' + fruitType;
    fruitFlesh.className = 'fruit-flesh ' + fruitType;
    
    // Специальные размеры для банана
    if (fruitType === 'banana') {
        fruitWrapper.classList.add('banana-wrapper');
    } else {
        fruitWrapper.classList.remove('banana-wrapper');
    }
    
    // Показываем область очистки
    fruitDisplay.style.display = 'flex';
    resetButton.style.display = 'none';
    peelButton.style.display = 'none';
    
    // Показываем подсказку
    const peelHint = document.getElementById('peelHint');
    if (peelHint) {
        peelHint.style.display = 'block';
    }
}

// Обработчик клика на кожуру фрукта
fruitSkin.addEventListener('click', () => {
    if (!selectedFruit || isPeeled) return;
    peelFruit();
});

// Обработчик очистки (кнопка - опционально, можно оставить для совместимости)
peelButton.addEventListener('click', () => {
    if (!selectedFruit || isPeeled) return;
    peelFruit();
});

// Функция очистки фрукта
function peelFruit() {
    if (isPeeled) return;
    
    isPeeled = true;
    
    // Анимация кожуры
    fruitSkin.classList.add('peeling');
    
    // Создаем кусочки кожуры
    createPeelPieces();
    
    // Показываем мякоть
    setTimeout(() => {
        fruitFlesh.classList.add('revealed');
        fruitFlesh.style.opacity = '1';
        fruitFlesh.style.visibility = 'visible';
        fruitSkin.style.visibility = 'hidden';
        resetButton.style.display = 'block';
        
        // Скрываем подсказку
        const peelHint = document.getElementById('peelHint');
        if (peelHint) {
            peelHint.style.display = 'none';
        }
    }, 1500);
}

// Создание кусочков кожуры
function createPeelPieces() {
    const pieceCount = 12;
    const colors = getPeelColor(selectedFruit);
    
    for (let i = 0; i < pieceCount; i++) {
        const piece = document.createElement('div');
        piece.className = 'peel-piece';
        
        // Позиция начала (вокруг фрукта)
        const angle = (i * 360 / pieceCount) * (Math.PI / 180);
        const radius = 120;
        const startX = Math.cos(angle) * radius;
        const startY = Math.sin(angle) * radius;
        
        // Случайное направление падения
        const fallAngle = angle + (Math.random() - 0.5) * Math.PI / 3;
        const fallDistance = 150 + Math.random() * 100;
        const tx = Math.cos(fallAngle) * fallDistance;
        const ty = Math.sin(fallAngle) * fallDistance + 100;
        
        piece.style.left = `calc(50% + ${startX}px)`;
        piece.style.top = `calc(50% + ${startY}px)`;
        piece.style.background = colors;
        piece.style.setProperty('--tx', `${tx}px`);
        piece.style.setProperty('--ty', `${ty}px`);
        piece.style.animationDelay = `${i * 0.1}s`;
        
        peelPieces.appendChild(piece);
    }
}

// Получение цвета кожуры для кусочков
function getPeelColor(fruitType) {
    const colors = {
        apple: 'linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%)',
        orange: 'linear-gradient(135deg, #ff9500 0%, #ff7700 100%)',
        banana: 'linear-gradient(135deg, #ffd700 0%, #ffb300 100%)',
        kiwi: 'linear-gradient(135deg, #8bc34a 0%, #689f38 100%)',
        mango: 'linear-gradient(135deg, #ff9800 0%, #f57c00 100%)',
        peach: 'linear-gradient(135deg, #ffb74d 0%, #ff9800 100%)'
    };
    return colors[fruitType] || colors.apple;
}

// Обработчик сброса
resetButton.addEventListener('click', () => {
    // Сбрасываем все
    fruitCards.forEach(c => c.classList.remove('selected'));
    selectedFruit = null;
    isPeeled = false;
    fruitDisplay.style.display = 'none';
    peelButton.style.display = 'none';
    resetButton.style.display = 'none';
    peelPieces.innerHTML = '';
    fruitWrapper.classList.remove('banana-wrapper');
    
    // Скрываем подсказку
    const peelHint = document.getElementById('peelHint');
    if (peelHint) {
        peelHint.style.display = 'none';
    }
});

// Инициализация
fruitDisplay.style.display = 'none';

