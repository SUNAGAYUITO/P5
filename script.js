// --- HTMLエスケープ関数 ---
function escapeHtml(text) {
    return $('<div>').text(String(text)).html();
}

// --- 共通のローディング表示関数 ---
function showLoading(element, message = "読み込み中...") {
    $(element).html(`
        <div class="flex items-center justify-center py-8">
            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-600 font-medium">${message}</span>
        </div>
    `);
}

// --- エラー表示関数 ---
function showError(element, message) {
    $(element).html(`
        <div class="flex items-center justify-center py-8 text-red-600">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <span class="font-medium">${message}</span>
        </div>
    `);
}

// --- 空データ表示関数 ---
function showEmpty(element, message, icon = "📝") {
    $(element).html(`
        <div class="text-center py-12">
            <div class="text-6xl mb-4">${icon}</div>
            <p class="text-gray-500 text-lg font-medium">${message}</p>
        </div>
    `);
}

// --- 成功メッセージ表示関数 ---
function showSuccessMessage(element, message) {
    $(element).html(`
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg animate-fade-in">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-green-700 font-medium">${message}</p>
            </div>
        </div>
    `);
    
    // 3秒後に自動で消す
    setTimeout(() => {
        $(element).fadeOut(500, () => $(element).empty().show());
    }, 3000);
}

// --- ランキング読み込み ---
function loadRanking() {
    showLoading("#ranking-list", "ランキングを読み込み中...");
    
    $.getJSON("ranking.php", function (res) {
        if (res.status === "success" && Array.isArray(res.data)) {
            if (res.data.length === 0) {
                showEmpty("#ranking-list", "ランキングデータがまだありません", "🏆");
                return;
            }
            
            let html = '<div class="space-y-4">';
            res.data.forEach(function (item, index) {
                const rankColor = index === 0 ? 'from-yellow-400 to-orange-400' : 
                                index === 1 ? 'from-gray-300 to-gray-400' : 
                                index === 2 ? 'from-orange-300 to-orange-400' : 
                                'from-emerald-400 to-teal-400';
                
                const rankIcon = index < 3 ? '👑' : '🏅';
                
                html += `
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 animate-slide-up" style="animation-delay: ${index * 0.1}s">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gradient-to-r ${rankColor} rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                    ${index + 1}
                                </div>
                            </div>
                            ${item.image_path ? 
                                `<img src="${escapeHtml(item.image_path)}" class="w-20 h-20 object-cover rounded-xl shadow-md" alt="${escapeHtml(item.menu_name)}">` : 
                                `<div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>`
                            }
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-2xl">${rankIcon}</span>
                                    <h3 class="font-bold text-xl text-gray-800">${escapeHtml(item.menu_name)}</h3>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="flex text-yellow-400">
                                        ${'★'.repeat(Math.floor(item.avg_rating))}${'☆'.repeat(5 - Math.floor(item.avg_rating))}
                                    </div>
                                    <span class="font-bold text-emerald-600">${item.avg_rating}</span>
                                    <span class="text-gray-500 text-sm">（${item.review_count}件のレビュー）</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $("#ranking-list").html(html);
        } else {
            showError("#ranking-list", "ランキング取得に失敗しました");
        }
    }).fail(function () {
        showError("#ranking-list", "通信エラーが発生しました");
    });
}

// --- AIおすすめ読み込み ---
function loadRecommendation() {
    $("#ai-recommend").html(`
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-6 rounded-xl shadow-md">
            <div class="flex items-center space-x-3">
                <div class="animate-spin">🤖</div>
                <span class="text-blue-700 font-medium">AIがおすすめメニューを選定中...</span>
            </div>
        </div>
    `);
    
    $.getJSON("recommend.php", function (res) {
        if (res.status === "success" && res.recommendation) {
            $("#ai-recommend").html(`
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400 p-6 rounded-xl shadow-md animate-fade-in">
                    <div class="flex items-start space-x-3">
                        <div class="text-2xl">🤖</div>
                        <div>
                            <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                AIのおすすめ
                            </h4>
                            <p class="text-blue-700 leading-relaxed">${escapeHtml(res.recommendation)}</p>
                        </div>
                    </div>
                </div>
            `);
        } else {
            $("#ai-recommend").html(`
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <p class="text-red-700">おすすめメニューの取得に失敗しました。</p>
                </div>
            `);
        }
    });
}

// --- ポイント合計読み込み ---
function loadCurrentPoint() {
    $.getJSON("point_total.php", function (res) {
        if (res.status === "success") {
            $("#current-point").html(`
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="font-bold text-emerald-600">${res.point}ポイント</span>
                </div>
            `);
        } else {
            $("#current-point").html(`
                <span class="text-red-600 font-medium">ポイント取得失敗</span>
            `);
        }
    }).fail(function () {
        $("#current-point").html(`
            <span class="text-red-600 font-medium">通信エラー</span>
        `);
    });
}

// --- ポイント履歴読み込み ---
function loadPointHistory() {
    showLoading("#point-list", "ポイント履歴を読み込み中...");
    
    $.getJSON("point_history.php", function (res) {
        if (res.status === "success" && Array.isArray(res.data)) {
            if (res.data.length === 0) {
                showEmpty("#point-list", "ポイント履歴はまだありません", "💰");
                return;
            }
            
            let html = '<div class="space-y-4">';
            res.data.forEach(function (item, index) {
                const isEarn = item.type === 'earn';
                const iconColor = isEarn ? 'text-green-500' : 'text-blue-500';
                const bgColor = isEarn ? 'from-green-50 to-emerald-50' : 'from-blue-50 to-indigo-50';
                const borderColor = isEarn ? 'border-green-200' : 'border-blue-200';
                const icon = isEarn ? '💰' : '🎁';
                
                html += `
                    <div class="bg-gradient-to-r ${bgColor} border ${borderColor} rounded-xl p-4 shadow-md hover:shadow-lg transition-all duration-300 animate-slide-up" style="animation-delay: ${index * 0.05}s">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="text-2xl">${icon}</div>
                                <div>
                                    <div class="font-bold text-gray-800">
                                        ${isEarn ? '獲得' : '使用'}：
                                        <span class="${iconColor} font-bold">${isEarn ? '+' : '-'}${item.points}ポイント</span>
                                    </div>
                                    <div class="text-gray-600 text-sm">${item.description || ''}</div>
                                    <div class="text-gray-500 text-xs mt-1">${item.created_at}</div>
                                </div>
                            </div>
                `;
                
                if (item.type === 'use') {
                    if (item.used === "1" || item.used === 1) {
                        html += `
                            <div class="flex items-center space-x-2 text-red-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="font-semibold">使用済み</span>
                            </div>
                        `;
                    } else {
                        html += `
                            <button class="use-btn bg-gradient-to-r from-blue-500 to-indigo-500 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-indigo-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 font-semibold" data-id="${item.id}">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                使用する
                            </button>
                        `;
                    }
                }
                
                html += `
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            $("#point-list").html(html);
        } else {
            showError("#point-list", "ポイント履歴の取得に失敗しました");
        }
    }).fail(function () {
        showError("#point-list", "通信エラーが発生しました");
    });
}

// --- jQuery 起動時処理 ---
$(function () {
    // 検索処理
    $("#search-btn").on("click", function () {
        const keyword = $("#keyword").val().trim();
        if (!keyword) {
            $("#results").html(`
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <p class="font-medium">キーワードを入力してください</p>
                </div>
            `);
            return;
        }
        
        showLoading("#results", "メニューを検索中...");
        
        $.get("./menu_search.php", { keyword }, function (res) {
            if (res.status === "success" && Array.isArray(res.data)) {
                if (res.data.length === 0) {
                    showEmpty("#results", "該当するメニューが見つかりません", "🔍");
                    return;
                }
                
                let html = '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">';
                res.data.forEach(function (menu, index) {
                    html += `
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-2 transition-all duration-300 overflow-hidden animate-scale-in" style="animation-delay: ${index * 0.1}s">
                            <div class="relative">
                                <img src="${escapeHtml(menu.image_path)}" alt="${escapeHtml(menu.name)}" class="w-full h-48 object-cover">
                                <div class="absolute top-3 right-3">
                                    <span class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                        ¥${escapeHtml(menu.price)}
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-lg text-gray-800 mb-2">${escapeHtml(menu.name)}</h3>
                                <div class="flex items-center justify-between">
                                    <div class="flex text-yellow-400">
                                        ${'★'.repeat(5)}
                                    </div>
                                    <button class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-4 py-2 rounded-lg hover:from-emerald-600 hover:to-teal-600 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-1 text-sm font-semibold">
                                        詳細を見る
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                $("#results").html(html);
            } else {
                showError("#results", "検索に失敗しました");
            }
        }, "json").fail(function () {
            showError("#results", "通信エラーが発生しました");
        });
    });

    // レビュー投稿処理
    $("#review-form").on("submit", function (e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        // ボタンをローディング状態に
        submitBtn.prop('disabled', true).html(`
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            投稿中...
        `);
        
        const formData = new FormData(this);
        
        $.ajax({
            url: "./review_post.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (res) {
                if (res.status === "success") {
                    showSuccessMessage("#review-message", res.message || "レビューを投稿しました！");
                    $("#review-form")[0].reset();
                    loadReviews();
                    loadCurrentPoint();
                } else {
                    $("#review-message").html(`
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                            <p class="text-red-700 font-medium">投稿に失敗：${res.message}</p>
                        </div>
                    `);
                }
            },
            error: function () {
                $("#review-message").html(`
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                        <p class="text-red-700 font-medium">通信エラーが発生しました</p>
                    </div>
                `);
            },
            complete: function () {
                // ボタンを元に戻す
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // レビュー一覧読み込み
    function loadReviews() {
        showLoading("#review-list", "レビューを読み込み中...");
        
        $.getJSON("./review_list.php", function (res) {
            if (res.status === "success" && Array.isArray(res.data)) {
                if (res.data.length === 0) {
                    showEmpty("#review-list", "まだレビューはありません", "📝");
                    return;
                }
                
                let html = '<div class="space-y-6">';
                res.data.forEach(function (review, index) {
                    html += `
                        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 animate-slide-up" data-id="${review.id}" style="animation-delay: ${index * 0.1}s">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full flex items-center justify-center text-white font-bold">
                                        ${escapeHtml(review.name).charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">${escapeHtml(review.name)}</h4>
                                        <p class="text-sm text-gray-500">${escapeHtml(review.indate)}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button class="edit-btn text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button class="delete-btn text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-semibold">${escapeHtml(review.menu_name)}</span>
                                    <div class="flex text-yellow-400">
                                        ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}
                                    </div>
                                    <span class="text-gray-600 text-sm">(${review.rating})</span>
                                </div>
                                <p class="comment text-gray-700 leading-relaxed">${escapeHtml(review.comment)}</p>
                            </div>
                            
                            ${review.image_path ? 
                                `<img src="${escapeHtml(review.image_path)}" class="w-full max-w-sm h-auto rounded-xl shadow-md" alt="レビュー画像">` : 
                                ''
                            }
                        </div>
                    `;
                });
                html += '</div>';
                $("#review-list").html(html);
            } else {
                showError("#review-list", "レビューの読み込みに失敗しました");
            }
        }).fail(function () {
            showError("#review-list", "通信エラーが発生しました");
        });
    }

    // 編集・削除ボタン
    $(document).on("click", ".edit-btn", function () {
        const parent = $(this).closest("div[data-id]");
        const id = parent.data("id");
        const oldComment = parent.find(".comment").text();
        const newComment = prompt("コメントを編集してください：", oldComment);
        
        if (newComment && newComment !== oldComment) {
            $.post("review_update.php", { id: id, comment: newComment }, function (res) {
                if (res.status === "success") {
                    loadReviews();
                    showSuccessMessage("#review-message", "レビューを更新しました");
                } else {
                    alert("更新失敗：" + res.message);
                }
            }, "json");
        }
    });

    $(document).on("click", ".delete-btn", function () {
        const parent = $(this).closest("div[data-id]");
        const id = parent.data("id");
        
        if (confirm("本当に削除しますか？")) {
            $.post("review_delete.php", { id: id }, function (res) {
                if (res.status === "success") {
                    loadReviews();
                    showSuccessMessage("#review-message", "レビューを削除しました");
                } else {
                    alert("削除失敗：" + res.message);
                }
            }, "json");
        }
    });

    // タブ切り替え
    $(".tab").on("click", function () {
        const selected = $(this).data("tab");
        
        $(".tab-content").addClass("hidden");
        $("#" + selected).removeClass("hidden").addClass("animate-fade-in");
        
        $(".tab").removeClass("tab-active").addClass("text-gray-600 hover:text-emerald-600 hover:bg-emerald-50");
        $(this).removeClass("text-gray-600 hover:text-emerald-600 hover:bg-emerald-50").addClass("tab-active");
        
        if (selected === "review") loadReviews();
        if (selected === "ranking") {
            loadRanking();
            loadRecommendation();
        }
        if (selected === "point") {
            loadCurrentPoint();
            loadPointHistory();
        }
    });

    // 特典使用処理
    $(document).on("click", ".use-btn", function () {
        const id = $(this).data("id");
        const btn = $(this);
        
        if (!confirm("この特典を使用済みにしますか？")) return;
        
        // ボタンをローディング状態に
        btn.prop('disabled', true).html(`
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            処理中...
        `);
        
        $.post("use_point.php", { id: id }, function (res) {
            if (res.status === "success") {
                alert("使用済みにしました");
                loadPointHistory();
            } else {
                alert("エラー：" + res.message);
                btn.prop('disabled', false).html(`
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    使用する
                `);
            }
        }, "json");
    });

    // 初期読み込み
    loadReviews();
    
    // Enterキーでの検索対応
    $("#keyword").on("keypress", function(e) {
        if (e.which === 13) {
            $("#search-btn").click();
        }
    });
});