export default function handler(req, res) {
  // 一時的な決済処理API
  // 本来はAuthorize.Net APIを使用する必要があります
  
  if (req.method === 'POST') {
    // 決済処理のロジックをここに実装
    // 現在は成功レスポンスを返すだけ
    
    res.status(200).json({
      success: true,
      message: 'Payment processed successfully',
      transactionId: 'temp_' + Date.now()
    });
  } else {
    res.status(405).json({ error: 'Method not allowed' });
  }
}