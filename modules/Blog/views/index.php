<div style="font-family: system-ui, -apple-system, sans-serif; padding: 2rem; max-width: 800px; margin: 0 auto;">
    <h1 style="color: #3b82f6; border-bottom: 2px solid #e5e7eb; padding-bottom: 1rem;"><?php echo $titulo; ?></h1>
    <p style="color: #4b5563; font-size: 1.1rem; line-height: 1.6;">Esta es una vista modular cargada desde <code>modules/Blog/views/index.php</code>.</p>
    
    <div style="margin-top: 2rem;">
        <?php foreach ($posts as $post): ?>
            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <h2 style="margin-top: 0; color: #1f2937;"><?php echo $post['title']; ?></h2>
                <p style="color: #4b5563; line-height: 1.5;"><?php echo $post['content']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    
    <a href="/" style="display: inline-block; margin-top: 1rem; background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; font-weight: 500;">Volver al inicio</a>
</div>
