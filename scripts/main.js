function playCompletionSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.2);
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => {
    // スライドナビゲーション機能
    const slides = document.querySelectorAll('.slide-section');
    let currentSlide = 0;
    

    // スクロール位置に基づいてナビゲーションを更新
    function updateNavigation() {
        const scrollPosition = window.scrollY + window.innerHeight / 2;
        const navLinks = document.querySelectorAll('.nav-link');
        
        slides.forEach((slide, index) => {
            const slideTop = slide.offsetTop;
            const slideBottom = slideTop + slide.offsetHeight;
            
            if (scrollPosition >= slideTop && scrollPosition < slideBottom) {
                if (currentSlide !== index) {
                    // ナビゲーションリンクを更新
                    navLinks.forEach(link => link.classList.remove('active'));
                    const currentLink = document.querySelector(`a[href="#${slide.id}"]`);
                    if (currentLink) {
                        currentLink.classList.add('active');
                    }
                    
                    
                    currentSlide = index;
                }
            }
        });
    }
    

    // スクロールイベントにデバウンスを適用
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        scrollTimeout = setTimeout(updateNavigation, 50);
    });

    // 初期化
    updateNavigation();

    // ハンバーガーメニューの実装
    const hamburger = document.getElementById('hamburger-menu');
    const mobileNav = document.getElementById('mobile-nav');
    const mobileNavClose = document.getElementById('mobile-nav-close');
    const mobileNavBackdrop = document.getElementById('mobile-nav-backdrop');
    

    // メニューを閉じる関数
    function closeMobileNav() {
        if (hamburger) {
            hamburger.classList.remove('active');
        }
        if (mobileNav) {
            mobileNav.classList.remove('active');
        }
        if (mobileNavBackdrop) {
            mobileNavBackdrop.classList.remove('active');
        }
        document.body.classList.remove('mobile-nav-open');
        
        // iOS Safari でのスクロール位置の修正
        if (document.body.style.position === 'fixed') {
            document.body.style.position = '';
            document.body.style.width = '';
            document.body.style.height = '';
        }
    }

    // メニューを開く関数
    function openMobileNav() {
        if (hamburger) {
            hamburger.classList.add('active');
        }
        if (mobileNav) {
            mobileNav.classList.add('active');
        }
        if (mobileNavBackdrop) {
            mobileNavBackdrop.classList.add('active');
        }
        document.body.classList.add('mobile-nav-open');
    }

    // ハンバーガーメニューのクリックイベント
    if (hamburger && mobileNav) {
        hamburger.addEventListener('click', function() {
            if (mobileNav.classList.contains('active')) {
                closeMobileNav();
            } else {
                openMobileNav();
            }
        });
    }

    // 閉じるボタンのクリックイベント
    if (mobileNavClose) {
        // クリックイベント
        mobileNavClose.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileNav();
        });
        
        // タッチデバイス用のフォールバック
        mobileNavClose.addEventListener('touchend', function(e) {
            e.preventDefault();
            e.stopPropagation();
            closeMobileNav();
        });
    }

    // メニューリンクをクリックしたらメニューを閉じる
    const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', closeMobileNav);
    });

    // 画面サイズが変わったらメニューを閉じる
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileNav();
        }
    });

    // バックドロップクリックでメニューを閉じる
    if (mobileNavBackdrop) {
        mobileNavBackdrop.addEventListener('click', closeMobileNav);
    }

    // オーバーレイクリックでメニューを閉じる（フォールバック）
    document.addEventListener('click', function(event) {
        if (mobileNav && mobileNav.classList.contains('active')) {
            // 閉じるボタンのクリックは既に他のイベントリスナーで処理されているので除外
            if (event.target.id === 'mobile-nav-close' || event.target.closest('#mobile-nav-close')) {
                return;
            }
            
            if (!hamburger.contains(event.target) && !mobileNav.contains(event.target)) {
                closeMobileNav();
            }
        }
    });


    const featureCards = document.querySelectorAll('.feature-card');
    const stepItems = document.querySelectorAll('.step-item');
    const allAnimatedElements = [...featureCards, ...stepItems];

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    allAnimatedElements.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'all 0.6s ease';
        observer.observe(item);
    });

    const heroModelCircle = document.querySelector('.hero-model-circle');
    if (heroModelCircle) {
        heroModelCircle.addEventListener('mouseenter', () => {
            heroModelCircle.style.transform = 'scale(1.05)';
            heroModelCircle.style.transition = 'all 0.3s ease';
        });
        
        heroModelCircle.addEventListener('mouseleave', () => {
            heroModelCircle.style.transform = 'scale(1)';
        });
    }

    const productCircle = document.querySelector('.product-circle');
    if (productCircle) {
        productCircle.addEventListener('mouseenter', () => {
            productCircle.style.transform = 'scale(1.05)';
            productCircle.style.transition = 'all 0.3s ease';
        });
        
        productCircle.addEventListener('mouseleave', () => {
            productCircle.style.transform = 'scale(1)';
        });
    }

    const leafDecorations = document.querySelectorAll('.leaf-decoration');
    leafDecorations.forEach((leaf, index) => {
        leaf.addEventListener('mouseenter', () => {
            leaf.style.transform = 'scale(1.2) rotate(20deg)';
            leaf.style.transition = 'all 0.3s ease';
        });
        
        leaf.addEventListener('mouseleave', () => {
            leaf.style.transform = '';
        });
    });

    const contactForm = document.querySelector('.contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const name = contactForm.querySelector('input[type="text"]').value;
            const email = contactForm.querySelector('input[type="email"]').value;
            const message = contactForm.querySelector('textarea').value;
            
            if (name && email && message) {
                const submitButton = contactForm.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                
                submitButton.textContent = '送信中...';
                submitButton.disabled = true;
                submitButton.style.background = '#9ca3af';
                
                setTimeout(() => {
                    const successMessage = document.createElement('div');
                    successMessage.style.cssText = `
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        background: white;
                        padding: 2rem;
                        border-radius: 16px;
                        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
                        z-index: 2000;
                        text-align: center;
                        max-width: 400px;
                    `;
                    
                    successMessage.innerHTML = `
                        <div style="font-size: 3rem; margin-bottom: 1rem;">✨</div>
                        <h3 style="color: #1f2937; margin-bottom: 1rem;">送信完了</h3>
                        <p style="color: #6b7280; margin-bottom: 1.5rem;">お問い合わせありがとうございます！<br>近日中にご連絡いたします。</p>
                        <button style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">閉じる</button>
                    `;
                    
                    const overlay = document.createElement('div');
                    overlay.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.5);
                        z-index: 1999;
                    `;
                    
                    document.body.appendChild(overlay);
                    document.body.appendChild(successMessage);
                    
                    const closeModal = () => {
                        document.body.removeChild(overlay);
                        document.body.removeChild(successMessage);
                        contactForm.reset();
                        submitButton.textContent = originalText;
                        submitButton.disabled = false;
                        submitButton.style.background = '#3b82f6';
                    };
                    
                    successMessage.querySelector('button').addEventListener('click', closeModal);
                    overlay.addEventListener('click', closeModal);
                    
                }, 1500);
            }
        });
    }

    // Simple navigation - let CSS scroll-snap handle the behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                // Let CSS scroll-snap handle the positioning
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    const buyButtons = document.querySelectorAll('.btn-primary, .btn-hero-primary');
    buyButtons.forEach(button => {
        if (button.textContent.includes('BUY NOW') || button.textContent.includes('今すぐ購入')) {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                const purchaseModal = document.createElement('div');
                purchaseModal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 2000;
                `;
                
                const modalContent = document.createElement('div');
                modalContent.style.cssText = `
                    background: white;
                    padding: 3rem;
                    border-radius: 20px;
                    text-align: center;
                    max-width: 500px;
                    position: relative;
                `;
                
                modalContent.innerHTML = `
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🦷✨</div>
                    <h2 style="color: #1f2937; margin-bottom: 1rem;">Premium Whitening Gel</h2>
                    <p style="color: #6b7280; margin-bottom: 2rem;">美しい笑顔への第一歩を踏み出しませんか？</p>
                    <div style="background: #f3f4f6; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                        <div style="font-size: 2rem; font-weight: 700; color: #3b82f6; margin-bottom: 0.5rem;">¥4,980</div>
                        <div style="color: #6b7280; font-size: 0.9rem;">送料無料・30日間返金保証</div>
                    </div>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button class="purchase-btn" style="background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">今すぐ購入</button>
                        <button class="close-btn" style="background: #e5e7eb; color: #374151; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600;">閉じる</button>
                    </div>
                `;
                
                purchaseModal.appendChild(modalContent);
                document.body.appendChild(purchaseModal);
                
                const closeModal = () => {
                    document.body.removeChild(purchaseModal);
                };
                
                modalContent.querySelector('.close-btn').addEventListener('click', closeModal);
                modalContent.querySelector('.purchase-btn').addEventListener('click', () => {
                    alert('ご購入ありがとうございます！\n商品は3-5営業日以内に発送いたします。');
                    closeModal();
                });
                
                purchaseModal.addEventListener('click', (e) => {
                    if (e.target === purchaseModal) {
                        closeModal();
                    }
                });
            });
        }
    });


    // パララックス効果の最適化
    let scrollTicking = false;
    
    const parallaxElements = document.querySelectorAll('.leaf-decoration');
    
    function updateParallax() {
        const scrollTop = window.pageYOffset;
        
        parallaxElements.forEach((element, index) => {
            const rect = element.getBoundingClientRect();
            // ビューポート内にある要素のみアニメーション
            if (rect.bottom >= 0 && rect.top <= window.innerHeight) {
                const speed = 0.5 + (index * 0.1);
                const yPos = -(scrollTop * speed * 0.3); // 効果を控えめに
                element.style.transform = `translateY(${yPos}px)`;
            }
        });
        
        scrollTicking = false;
    }
    
    window.addEventListener('scroll', () => {
        if (!scrollTicking) {
            requestAnimationFrame(updateParallax);
            scrollTicking = true;
        }
    });

    // ニュースレター登録機能
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const emailInput = newsletterForm.querySelector('.newsletter-input');
            const checkbox = newsletterForm.querySelector('input[type="checkbox"]');
            const submitBtn = newsletterForm.querySelector('.newsletter-btn');
            
            if (!emailInput.value || !checkbox.checked) {
                alert('メールアドレスを入力し、プライバシーポリシーに同意してください。');
                return;
            }
            
            // ボタンの状態を変更
            const originalText = submitBtn.textContent;
            submitBtn.textContent = '登録中...';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.7';
            
            // シミュレートされた登録処理
            setTimeout(() => {
                // 成功モーダルを表示
                const successModal = document.createElement('div');
                successModal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 10000;
                `;
                
                successModal.innerHTML = `
                    <div style="
                        background: white;
                        padding: 3rem;
                        border-radius: 20px;
                        text-align: center;
                        max-width: 400px;
                        margin: 2rem;
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    ">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">📧</div>
                        <h3 style="color: #1f2937; margin-bottom: 1rem; font-size: 1.5rem;">登録完了！</h3>
                        <p style="color: #6b7280; margin-bottom: 2rem; line-height: 1.6;">
                            ニュースレターの登録が完了しました。<br>
                            最新情報をメールでお届けします。
                        </p>
                        <button onclick="this.parentElement.parentElement.remove()" style="
                            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            border-radius: 8px;
                            cursor: pointer;
                            font-weight: 600;
                            transition: all 0.3s ease;
                        ">閉じる</button>
                    </div>
                `;
                
                document.body.appendChild(successModal);
                
                // フォームをリセット
                emailInput.value = '';
                checkbox.checked = false;
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                
                // モーダルのクリックで閉じる
                successModal.addEventListener('click', (e) => {
                    if (e.target === successModal) {
                        successModal.remove();
                    }
                });
                
            }, 1500);
        });
    }
});
// 購入ボタンハンドラー（グローバル関数として定義）
function handlePurchase() {
    // モバイルメニューを閉じる
    const mobileNav = document.getElementById("mobile-nav");
    const backdrop = document.getElementById("mobile-nav-backdrop");
    if (mobileNav && mobileNav.classList.contains("active")) {
        mobileNav.classList.remove("active");
        backdrop.classList.remove("active");
        document.body.style.overflow = "";
    }
    
    // Authorize.Net Simple Checkoutボタンがフォーム送信で処理されるため、
    // JavaScript遷移は不要です。
}
