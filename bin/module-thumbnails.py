"""Generates ACF Extended module thumbnails (400x250 SVG wireframes).

One drawing function per page_modules layout, named after the layout. Add one
when you add a module, add it to MODULES, then regenerate (from the theme
folder):

    python -I bin/module-thumbnails.py

Writes assets/images/modules/{layout}.svg (pass a folder to write somewhere
else). drift_acfe_module_thumbnail() in inc/acf.php serves them in the ACF
Extended "Add module" picker. A .jpg/.png/.webp with the same name (e.g. a
real screenshot) takes priority over the .svg.

Colours follow the theme tokens (assets/site.css): black bands, soft grey
tiles, suite accents. Kept in this file only; the thumbnails are never shown
on the front end.
"""
import os
import sys

W, H = 400, 250
WHITE = '#ffffff'
BLACK = '#08090b'
SOFT = '#f5f6f7'
LINE = '#e3e5e8'
TEXT = '#c9ccd1'        # body-text lines
BAND_TEXT = '#4a4d53'   # body-text lines on black
IMG = '#d5d8dd'
IMG_DARK = '#b3b8bf'
MUSIC = '#ff4fa3'
AGENCY = '#1f7bff'
SEO = '#57e35b'
ACCENT = MUSIC


class Svg:
    def __init__(self, bg=WHITE):
        self.parts = [f'<rect width="{W}" height="{H}" fill="{bg}"/>']

    def rect(self, x, y, w, h, fill, r=0, stroke=None, dash=False, opacity=None):
        extra = f' stroke="{stroke}" stroke-width="1.5"' if stroke else ''
        extra += ' stroke-dasharray="5 4"' if dash else ''
        extra += f' fill-opacity="{opacity}"' if opacity is not None else ''
        self.parts.append(f'<rect x="{x:g}" y="{y:g}" width="{w:g}" height="{h:g}" rx="{r:g}" fill="{fill}"{extra}/>')

    def circle(self, cx, cy, r, fill='none', stroke=None, dash=False):
        extra = f' stroke="{stroke}" stroke-width="2"' if stroke else ''
        extra += ' stroke-dasharray="3 3"' if dash else ''
        self.parts.append(f'<circle cx="{cx:g}" cy="{cy:g}" r="{r:g}" fill="{fill}"{extra}/>')

    def path(self, d, fill='none', stroke=None, width=2):
        s = f' stroke="{stroke}" stroke-width="{width}" stroke-linecap="round" stroke-linejoin="round"' if stroke else ''
        self.parts.append(f'<path d="{d}" fill="{fill}"{s}/>')

    def image(self, x, y, w, h, r=0, dark=False):
        """Photo placeholder: tinted box with a sun and two hills, clipped."""
        cid = f'c{len(self.parts)}'
        fill, hill, sun = ('#2a2b2e', '#3d3f44', '#55585e') if dark else (IMG, IMG_DARK, '#eceef1')
        self.parts.append(f'<clipPath id="{cid}"><rect x="{x:g}" y="{y:g}" width="{w:g}" height="{h:g}" rx="{r:g}"/></clipPath>')
        self.parts.append(f'<g clip-path="url(#{cid})">')
        self.rect(x, y, w, h, fill)
        self.circle(x + w * 0.72, y + h * 0.3, min(w, h) * 0.09, sun)
        self.path(f'M{x:g} {y + h:g} L{x + w * 0.32:g} {y + h * 0.5:g} L{x + w * 0.58:g} {y + h:g} Z', fill=hill)
        self.path(f'M{x + w * 0.38:g} {y + h:g} L{x + w * 0.7:g} {y + h * 0.58:g} L{x + w:g} {y + h * 0.9:g} L{x + w:g} {y + h:g} Z', fill=hill)
        self.parts.append('</g>')

    def lines(self, x, y, w, n=3, gap=9, h=4, fill=TEXT, last=0.6, align='left'):
        for i in range(n):
            lw = w * (last if i == n - 1 and n > 1 else 1)
            lx = x + (w - lw) / 2 if align == 'center' else x
            self.rect(lx, y + i * gap, lw, h, fill, r=h / 2)

    def heading(self, x, y, w, fill=BLACK, h=10):
        self.rect(x, y, w, h, fill, r=h / 2)

    def eyebrow(self, x, y, accent=ACCENT, fill=TEXT):
        """Accent rule + small caps label, as .eyebrow--accent."""
        self.rect(x, y, 14, 3, accent, r=1.5)
        self.rect(x + 20, y - 0.5, 46, 4, fill, r=2)

    def head(self, x=30, y=26, w=170, accent=ACCENT, lead=True, dark=False):
        """Section header (eyebrow, h2, lead). Returns the y below it."""
        self.eyebrow(x, y, accent, BAND_TEXT if dark else TEXT)
        self.heading(x, y + 14, w, WHITE if dark else BLACK, h=12)
        if lead:
            self.lines(x, y + 36, w * 1.15, n=1, fill=BAND_TEXT if dark else TEXT)
            return y + 56
        return y + 40

    def button(self, x, y, w=50, h=14, fill=BLACK, line=False):
        if line:
            self.rect(x, y, w, h, 'none', r=h / 2, stroke=fill)
        else:
            self.rect(x, y, w, h, fill, r=h / 2)

    def play(self, cx, cy, r=18, fill=ACCENT):
        self.circle(cx, cy, r, fill)
        self.path(f'M{cx - r * 0.3:g} {cy - r * 0.42:g} L{cx + r * 0.45:g} {cy:g} L{cx - r * 0.3:g} {cy + r * 0.42:g} Z', fill=WHITE)

    def arrow_btn(self, cx, cy, left=True, colour=BLACK):
        self.circle(cx, cy, 9, stroke=colour)
        d = f'M{cx + 2} {cy - 4} L{cx - 2} {cy} L{cx + 2} {cy + 4}' if left else f'M{cx - 2} {cy - 4} L{cx + 2} {cy} L{cx - 2} {cy + 4}'
        self.path(d, stroke=colour, width=1.5)

    def field(self, x, y, w, h=16):
        self.rect(x, y, w, h, WHITE, r=4, stroke=LINE)

    def svg(self):
        return f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}">{"".join(self.parts)}</svg>\n'


def columns(n, x0=30, gap=12, width=W - 60):
    """x positions and width of n equal columns."""
    cw = (width - gap * (n - 1)) / n
    return [x0 + i * (cw + gap) for i in range(n)], cw


# --- One function per module -------------------------------------------------

def hero():
    s = Svg()
    s.eyebrow(30, 62)
    s.heading(30, 78, 200, h=18)
    s.heading(30, 102, 150, h=18)
    s.lines(30, 134, 190, n=2)
    s.button(30, 164, 64, 18)
    s.button(102, 164, 64, 18, line=True)
    s.rect(262, 50, 110, 150, BLACK, r=12)
    s.rect(290, 102, 54, 46, ACCENT, r=10)
    return s


def content():
    s = Svg()
    s.heading(80, 34, 180, h=16)
    for i, y in enumerate((70, 116, 162)):
        s.lines(80, y, 240, n=4, last=0.5 if i == 2 else 0.8)
    return s


def split_text():
    s = Svg()
    s.eyebrow(30, 60)
    s.heading(30, 74, 130, h=14)
    s.heading(30, 94, 90, h=14)
    s.lines(210, 60, 160, n=5)
    s.lines(210, 116, 160, n=5, last=0.4)
    return s


def suite_tiles():
    s = Svg()
    s.head(lead=False)
    xs, cw = columns(3)
    for x, accent in zip(xs, (MUSIC, AGENCY, SEO)):
        s.rect(x, 82, cw, 140, SOFT, r=8)
        s.rect(x, 82, cw, 5, accent, r=2)
        s.heading(x + 12, 102, cw * 0.55, h=8)
        s.lines(x + 12, 118, cw - 24, n=2, gap=8, h=3)
        for i in range(3):
            s.rect(x + 12 + i * 24, 146, 18, 18, accent, r=5)
        s.lines(x + 12, 196, cw * 0.5, n=1, h=3)
    return s


def app_card(s, x, y, w, h, accent, featured=False):
    bg, fg = (BLACK, WHITE) if featured else (WHITE, BLACK)
    s.rect(x, y, w, h, bg, r=8, stroke=None if featured else LINE)
    s.rect(x + 10, y + 10, 18, 18, accent, r=5)
    s.heading(x + 10, y + 38, w * 0.5, fg, h=7)
    s.lines(x + 10, y + 52, w - 20, n=2, gap=7, h=3, fill=BAND_TEXT if featured else TEXT)


def app_suites():
    s = Svg()
    s.eyebrow(30, 26)
    s.heading(30, 38, 120, h=11)
    for i, label in enumerate(('All', 'iOS', 'Web')):
        s.rect(250 + i * 40, 34, 34, 14, BLACK if i == 0 else SOFT, r=7)
    xs, cw = columns(3)
    app_card(s, xs[0], 66, cw * 2 + 12, 76, ACCENT, featured=True)
    app_card(s, xs[2], 66, cw, 76, ACCENT)
    for x in xs:
        app_card(s, x, 152, cw, 76, ACCENT)
    return s


def stats():
    s = Svg()
    s.head(lead=False)
    xs, cw = columns(4)
    for x in xs:
        s.rect(x, 100, cw, 1.5, LINE)
        s.rect(x, 99, 22, 3, ACCENT)
        s.rect(x, 118, cw * 0.55, 28, BLACK, r=4)
        s.lines(x, 160, cw * 0.8, n=2, gap=8, h=3)
    return s


def cards():
    s = Svg()
    s.head()
    xs, cw = columns(3)
    for x in xs:
        s.rect(x, 96, cw, 126, SOFT, r=8)
        s.rect(x + 12, 108, 24, 24, ACCENT, r=6)
        s.heading(x + 12, 144, cw * 0.6, h=7)
        s.lines(x + 12, 160, cw - 24, n=3, gap=8, h=3)
        s.lines(x + 12, 202, 30, n=1, h=3, fill=BLACK)
    return s


def spotlight():
    s = Svg(BLACK)
    s.head(dark=True, w=150)
    for i in range(3):
        y = 100 + i * 34
        s.circle(40, y + 6, 8, ACCENT)
        s.heading(56, y, 90, WHITE, h=6)
        s.lines(56, y + 12, 120, n=1, h=3, fill=BAND_TEXT)
    s.button(30, 208, 60, 16, WHITE)
    s.button(98, 208, 60, 16, WHITE, line=True)
    for i, x in enumerate((230, 285, 340)):
        s.rect(x, 104, 38, 38, '#1c1d20', r=8, stroke='#2e3034')
        if i < 2:
            s.path(f'M{x + 40} 123 L{x + 53} 123', stroke=ACCENT, width=2)
    s.rect(244, 118, 10, 10, ACCENT, r=3)
    return s


def image_text():
    s = Svg()
    s.image(30, 40, 160, 170, r=10)
    s.eyebrow(214, 70)
    s.heading(214, 84, 130, h=13)
    s.lines(214, 110, 150, n=4)
    s.button(214, 160, 56, 16)
    return s


def full_image():
    s = Svg()
    s.image(0, 0, W, H, dark=True)
    s.rect(0, 120, W, 130, BLACK, opacity=0.6)
    s.heading(30, 170, 190, WHITE, h=14)
    s.lines(30, 194, 160, n=1, fill='#9ea3aa')
    s.button(30, 210, 56, 16, WHITE)
    return s


def video():
    s = Svg()
    s.rect(30, 28, 340, 190, BLACK, r=12)
    s.image(30, 28, 340, 190, r=12, dark=True)
    s.play(200, 123, 22)
    s.lines(130, 230, 140, n=1, h=3, align='center')
    return s


def pricing():
    s = Svg()
    s.head(x=115, w=170, lead=False)
    xs, cw = columns(3)
    for i, x in enumerate(xs):
        featured = i == 1
        bg, fg = (BLACK, WHITE) if featured else (SOFT, BLACK)
        s.rect(x, 80, cw, 150, bg, r=8)
        if featured:
            s.rect(x + cw - 46, 90, 36, 11, ACCENT, r=5.5)
        s.heading(x + 12, 94, cw * 0.4, fg, h=7)
        s.heading(x + 12, 110, cw * 0.5, fg, h=16)
        for j in range(3):
            y = 140 + j * 13
            s.path(f'M{x + 12} {y + 2} L{x + 15} {y + 5} L{x + 20} {y - 1}', stroke=ACCENT, width=1.5)
            s.rect(x + 26, y, cw * 0.55, 3, BAND_TEXT if featured else TEXT, r=1.5)
        s.button(x + 12, 202, cw - 24, 16, ACCENT if featured else BLACK, line=not featured)
    return s


def table():
    s = Svg()
    s.head(lead=False)
    s.rect(30, 80, 340, 146, WHITE, r=8, stroke=LINE)
    s.rect(30, 80, 340, 24, BLACK, r=8)
    s.rect(30, 96, 340, 8, BLACK)
    for x in (42, 200, 260, 320):
        s.rect(x, 89, 34, 5, WHITE, r=2.5)
    for i in range(5):
        y = 104 + i * 24.4
        if i == 4:
            s.rect(30, y, 340, 24, SOFT)
            s.rect(30, y, 4, 24, ACCENT)
        elif i:
            s.rect(30, y, 340, 1, LINE)
        s.rect(42, y + 10, 90, 4, BLACK if i == 4 else TEXT, r=2)
        for x in (200, 260, 320):
            s.rect(x, y + 10, 28, 4, BLACK if i == 4 else TEXT, r=2)
    return s


def team():
    s = Svg()
    s.head(lead=False)
    xs, cw = columns(4)
    for x, accent in zip(xs, (MUSIC, AGENCY, SEO, BLACK)):
        s.image(x, 80, cw, cw, r=8)
        s.rect(x, 80 + cw - 4, cw, 4, accent)
        s.heading(x, 80 + cw + 12, cw * 0.7, h=7)
        s.lines(x, 80 + cw + 26, cw * 0.5, n=1, h=3)
        s.rect(x, 80 + cw + 40, cw, 1, LINE)
    return s


def testimonials():
    s = Svg()
    s.head(lead=False)
    xs, cw = columns(3)
    for x in xs:
        s.rect(x, 82, cw, 140, SOFT, r=8)
        s.parts.append(f'<text x="{x + 10:g}" y="{118}" font-family="Georgia, serif" font-size="36" font-weight="700" fill="{ACCENT}">“</text>')
        s.lines(x + 12, 124, cw - 24, n=4, gap=9, h=4, fill='#a9adb4')
        s.circle(x + 24, 200, 10, IMG_DARK)
        s.heading(x + 40, 194, cw * 0.4, h=5)
        s.lines(x + 40, 204, cw * 0.3, n=1, h=3)
    return s


def logo_mark(s, x, y, w, h, i):
    """Simple stand-in logo shapes so the row doesn't look like one repeated tile."""
    cx, cy = x + w / 2, y + h / 2
    g = '#9ea3aa'
    if i % 3 == 0:
        s.circle(cx - 14, cy, 7, g)
        s.rect(cx - 4, cy - 3, 24, 6, g, r=3)
    elif i % 3 == 1:
        s.rect(cx - 18, cy - 6, 12, 12, g, r=2)
        s.rect(cx - 2, cy - 2.5, 22, 5, g, r=2.5)
    else:
        s.rect(cx - 20, cy - 4, 40, 8, g, r=4)


def logos():
    s = Svg()
    s.head(lead=False)
    s.arrow_btn(342, 56)
    s.arrow_btn(366, 56, left=False)
    # Five tiles with the last one running off the edge, to show it scrolls.
    xs, cw = columns(5, gap=10, width=400)
    for i, x in enumerate(xs):
        th = cw * 2 / 3
        s.rect(x, 104, cw, th, SOFT, r=6)
        logo_mark(s, x, 104, cw, th, i)
    return s


def roadmap():
    s = Svg()
    s.head(lead=False)
    xs, cw = columns(3)
    markers = ('solid', 'ring', 'dash')
    for x, m in zip(xs, markers):
        if m == 'solid':
            s.circle(x + 6, 90, 5, BLACK)
        else:
            s.circle(x + 6, 90, 4.5, stroke=BLACK, dash=m == 'dash')
        s.heading(x + 18, 86, 40, h=7)
        for j, accent in enumerate((MUSIC, AGENCY, SEO)[: 3 if m == 'solid' else 2]):
            y = 106 + j * 40
            s.rect(x, y, cw, 32, WHITE, r=6, stroke=LINE)
            s.rect(x, y, 4, 32, accent, r=2)
            s.heading(x + 12, y + 9, cw * 0.55, h=5)
            s.lines(x + 12, y + 20, cw * 0.4, n=1, h=3)
    return s


def faq():
    s = Svg()
    s.head(x=80, w=160, lead=False)
    for i in range(4):
        y = 84 + i * 38
        s.rect(80, y, 240, 1, LINE)
        s.heading(80, y + 14, 150 - i * 12, h=7)
        s.rect(306, y + 16.5, 10, 2, BLACK, r=1)
        if i:
            s.rect(310, y + 12.5, 2, 10, BLACK, r=1)
    s.lines(80, 109, 200, n=1, h=3)
    s.rect(80, 236, 240, 1, LINE)
    return s


def cta():
    s = Svg()
    s.rect(20, 50, 360, 150, BLACK, r=14)
    s.eyebrow(150, 78, fill=BAND_TEXT)
    s.heading(110, 94, 180, WHITE, h=16)
    s.lines(130, 122, 140, n=1, fill=BAND_TEXT)
    s.button(142, 150, 56, 18, ACCENT)
    s.button(206, 150, 56, 18, WHITE, line=True)
    return s


def contact():
    s = Svg(SOFT)
    s.eyebrow(30, 50)
    s.heading(30, 64, 120, h=14)
    s.lines(30, 90, 130, n=3)
    s.field(200, 40, 82)
    s.field(290, 40, 82)
    s.field(200, 66, 172)
    for i in range(3):
        s.rect(200 + i * 56, 92, 50, 14, WHITE, r=7, stroke=LINE)
    s.field(200, 116, 172, 56)
    s.button(200, 184, 70, 18)
    return s


def newsletter():
    s = Svg()
    s.eyebrow(30, 92)
    s.heading(30, 106, 140, h=14)
    s.lines(30, 132, 150, n=2)
    s.lines(210, 98, 60, n=1, h=3, fill=BLACK)
    s.field(210, 110, 110, 20)
    s.button(326, 110, 46, 20, ACCENT)
    s.lines(210, 140, 130, n=1, h=3)
    return s


MODULES = [
    hero, content, suite_tiles, app_suites, stats, cards, spotlight, split_text, image_text, full_image,
    video, pricing, table, team, testimonials, logos, roadmap, faq, cta, contact, newsletter,
]


def write(functions, out):
    os.makedirs(out, exist_ok=True)
    for fn in functions:
        with open(os.path.join(out, fn.__name__ + '.svg'), 'w', encoding='utf-8', newline='\n') as f:
            f.write(fn().svg())
    print(len(functions), 'thumbnails written to', out)


if __name__ == '__main__':
    write(MODULES, sys.argv[1] if len(sys.argv) > 1 else 'assets/images/modules')
