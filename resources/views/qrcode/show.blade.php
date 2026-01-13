<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $credential->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .credential-info {
            color: #666;
            font-size: 14px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .qr-wrapper {
            display: inline-block;
            padding: 20px;
            background: white;
            border: 2px solid #e5e7eb;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .qr-wrapper svg {
            display: block;
            width: 300px;
            height: 300px;
        }

        .url-display {
            background: #f9fafb;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            word-break: break-all;
            font-size: 12px;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        @media print {
            body {
                background: white;
            }

            .container {
                box-shadow: none;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $credential->title }}</h1>
        <div class="credential-info">
            <strong>{{ $credential->student->name }}</strong><br>
            Issued by {{ $credential->issuer->name }}
        </div>

        <div class="qr-wrapper">
            {!! $qrCode !!}
        </div>

        <div class="url-display">
            {{ $url }}
        </div>

        <div class="actions">
            <button onclick="window.print()" class="btn btn-primary">
                Print QR Code
            </button>
            <button onclick="downloadQR()" class="btn btn-primary">
                Download PNG
            </button>
            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">
                Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        function downloadQR() {
            const svg = document.querySelector('.qr-wrapper svg');
            const svgData = new XMLSerializer().serializeToString(svg);
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();

            canvas.width = 300;
            canvas.height = 300;

            img.onload = function() {
                ctx.fillStyle = 'white';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0);

                canvas.toBlob(function(blob) {
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = '{{ Str::slug($credential->title) }}-qr-code.png';
                    a.click();
                    URL.revokeObjectURL(url);
                });
            };

            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
        }
    </script>
</body>
</html>
