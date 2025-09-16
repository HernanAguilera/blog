<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Preview' }} - BlogV2 Preview</title>
    @if($meta_description ?? false)
    <meta name="description" content="{{ $meta_description }}">
    @endif
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
        }

        .preview-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .preview-header {
            background: #e3f2fd;
            color: #1565c0;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
            border-left: 4px solid #2196f3;
        }

        .preview-header h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
        }

        .preview-header p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
        }

        .content-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .content-excerpt {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
            font-style: italic;
            padding: 20px;
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            border-radius: 4px;
        }

        .content-body {
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .content-body h1, .content-body h2, .content-body h3,
        .content-body h4, .content-body h5, .content-body h6 {
            margin-top: 30px;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .content-body h1 { font-size: 2rem; }
        .content-body h2 { font-size: 1.8rem; }
        .content-body h3 { font-size: 1.6rem; }

        .content-body p {
            margin-bottom: 20px;
        }

        .content-body pre {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            overflow-x: auto;
            margin: 20px 0;
        }

        .content-body code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Monaco', 'Consolas', monospace;
        }

        .content-body pre code {
            background: none;
            padding: 0;
        }

        .content-body blockquote {
            border-left: 4px solid #007bff;
            padding-left: 20px;
            margin: 20px 0;
            color: #666;
            font-style: italic;
        }

        .content-body img {
            max-width: 100%;
            height: auto;
            border-radius: 6px;
            margin: 20px 0;
        }

        .content-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .content-body th, .content-body td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .content-body th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .preview-footer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            background: #fff3cd;
            border-radius: 6px;
            color: #856404;
        }

        .expires-info {
            font-size: 14px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .preview-container {
                padding: 20px;
            }

            .content-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h4>🔍 Vista Previa - BlogV2</h4>
            <p>Esta es una vista previa temporal. El contenido puede diferir de la versión final publicada.</p>
        </div>

        @if($title ?? false)
        <h1 class="content-title">{{ $title }}</h1>
        @endif

        @if($excerpt ?? false)
        <div class="content-excerpt">
            {{ $excerpt }}
        </div>
        @endif

        <div class="content-body">
            {!! $content !!}
        </div>

        <div class="preview-footer">
            <p><strong>🔒 Enlace de vista previa temporal</strong></p>
            <p class="expires-info">
                Esta vista previa expira el: <strong>{{ \Carbon\Carbon::parse($expires_at)->format('d/m/Y H:i') }}</strong>
            </p>
        </div>
    </div>
</body>
</html>