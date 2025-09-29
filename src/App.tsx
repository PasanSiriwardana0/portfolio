import { Button } from '@/components/ui/button';
import {
  Github,
  Linkedin,
  Mail,
  Download,
  ExternalLink,
  Code2,
  Rocket,
  Sparkles,
  Moon,
  Sun,
  Menu,
  X,
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { useEffect, useState, createContext, useContext, type ReactNode } from 'react';
import cvPdf from '@/assets/downloads/cv.pdf';

// Theme persistence helpers (cookies)
type ThemeCookie = 'light' | 'dark' | 'system';
function getCookie(name: string): string | null {
  const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'));
  return match ? decodeURIComponent(match[1]) : null;
}
function setCookie(name: string, value: string, days = 365) {
  const date = new Date();
  date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
  document.cookie = `${name}=${encodeURIComponent(value)}; expires=${date.toUTCString()}; path=/; SameSite=Lax`;
}

// Data
interface Project {
  title: string;
  description: string;
  tags: string[];
  link: string;
}

interface ExperienceItem {
  role: string;
  company: string;
  period: string;
  details: string;
}

const projects: Project[] = [
  {
    title: 'DeFi Yield Optimizer',
    description:
      'A decentralized finance platform that auto-compounds yields across multiple liquidity pools.',
    tags: ['Solidity', 'Ethereum', 'DeFi'],
    link: '#',
  },
  {
    title: 'NFT Marketplace',
    description:
      'Full-stack NFT marketplace with auctions, royalty support, and IPFS-based metadata storage.',
    tags: ['Solidity', 'React', 'IPFS', 'Web3.js'],
    link: '#',
  },
  {
    title: 'DAO Governance Platform',
    description:
      'On-chain voting and treasury management system for decentralized organizations.',
    tags: ['DAO', 'Smart Contracts', 'Ethers.js'],
    link: '#',
  },
];

const skills: string[] = [
  'Solidity',
  'Ethereum',
  'Smart Contracts',
  'Web3.js / Ethers.js',
  'Hardhat / Truffle',
  'Polygon',
  'IPFS',
  'DeFi Protocols',
  'Blockchain Security',
  'Cryptography',
];

const experience: ExperienceItem[] = [
  {
    role: 'Blockchain Engineer',
    company: 'Cosmos Labs',
    period: '2023 — Present',
    details:
      'Designed and deployed smart contracts, optimized gas usage, and integrated DeFi protocols.',
  },
  {
    role: 'Smart Contract Developer',
    company: 'Nova Works',
    period: '2021 — 2023',
    details:
      'Built and audited Solidity contracts, integrated NFT marketplaces, and enhanced dApp security.',
  },
  {
    role: 'Software Engineer',
    company: 'Orion Studio',
    period: '2019 — 2021',
    details:
      'Contributed to full-stack blockchain apps and collaborated with teams on Web3 integrations.',
  },
];

// Theme Context
interface ThemeContextValue {
  isDark: boolean;
  toggleTheme: () => void;
}

const ThemeContext = createContext<ThemeContextValue>({
  isDark: false,
  toggleTheme: () => {},
});

function Section({
  id,
  className,
  children,
}: {
  id?: string;
  className?: string;
  children: ReactNode;
}) {
  return (
    <section id={id} className={cn('relative py-20 md:py-28', className)}>
      {children}
    </section>
  );
}

function NeonBackground() {
  const { isDark } = useContext(ThemeContext);

  return (
    <>
      <div className="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div
          className={cn(
            'absolute -top-40 left-1/2 size-[60rem] -translate-x-1/2 rounded-full blur-3xl transition-all duration-1000',
            isDark ? 'bg-primary/20' : 'bg-primary/10',
          )}
        />
        <div
          className={cn(
            'absolute -bottom-40 right-1/2 size-[60rem] translate-x-1/2 rounded-full blur-3xl transition-all duration-1000',
            isDark ? 'bg-purple-600/20' : 'bg-purple-500/10',
          )}
        />
        <div
          className={cn(
            'absolute inset-0 transition-all duration-1000',
            isDark
              ? 'bg-[radial-gradient(ellipse_at_top,rgba(120,119,198,0.15),transparent_60%)]'
              : 'bg-[radial-gradient(ellipse_at_top,rgba(255,255,255,0.06),transparent_60%)]',
          )}
        />
        {/* Animated grid */}
        <div className="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]" />
      </div>
    </>
  );
}

function Header() {
  const { isDark, toggleTheme } = useContext(ThemeContext);
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const navItems = [
    { href: '#about', label: 'About' },
    { href: '#skills', label: 'Skills' },
    { href: '#projects', label: 'Projects' },
    { href: '#experience', label: 'Experience' },
    { href: '#contact', label: 'Contact' },
  ];

  const handleNavClick = (href: string) => {
    setIsMobileMenuOpen(false);
    const element = document.querySelector(href);
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <header
      className={cn(
        'sticky top-0 z-50 transition-all duration-300',
        isScrolled
          ? 'backdrop-blur-xl bg-background/80 border-b border-border/50 shadow-lg'
          : 'backdrop-blur-md bg-background/40 border-b border-border/20',
      )}
    >
      <div className="mx-auto w-full max-w-[var(--container-figma)] px-4 md:px-6">
        <div className="flex h-16 items-center justify-between">
          <a
            href="#hero"
            className="flex items-center gap-2 text-sm font-semibold group"
            onClick={(e) => {
              e.preventDefault();
              handleNavClick('#hero');
            }}
          >
            <div className="relative">
              <Sparkles className="size-5 text-primary transition-transform duration-300 group-hover:scale-110" />
              <div className="absolute inset-0 bg-primary/20 rounded-full blur-sm group-hover:blur-md transition-all duration-300" />
            </div>
            <span className="tracking-tight bg-gradient-to-r from-primary to-purple-600 bg-clip-text text-transparent">
              Pasan Siriwardana
            </span>
          </a>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center gap-1">
            {navItems.map((item) => (
              <button
                key={item.href}
                onClick={() => handleNavClick(item.href)}
                className="group relative px-4 py-2 rounded-lg transition-all duration-300 hover:bg-accent/20"
              >
                <span className="relative z-10 text-sm font-medium">{item.label}</span>
                <div className="absolute inset-0 bg-gradient-to-r from-primary/10 to-purple-600/10 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
              </button>
            ))}
          </nav>

          <div className="flex items-center gap-2">
            {/* Theme Toggle */}
            <button
              onClick={toggleTheme}
              className="relative p-2 rounded-lg border border-border/30 bg-background/50 hover:bg-accent/20 transition-all duration-300 group"
            >
              <div className="relative size-5">
                <Sun
                  className={cn(
                    'absolute size-5 transition-all duration-300',
                    isDark ? 'rotate-90 opacity-0' : 'rotate-0 opacity-100',
                  )}
                />
                <Moon
                  className={cn(
                    'absolute size-5 transition-all duration-300',
                    isDark ? 'rotate-0 opacity-100' : '-rotate-90 opacity-0',
                  )}
                />
              </div>
              <div className="absolute inset-0 bg-primary/5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
            </button>

            {/* Social Links */}
            <div className="hidden md:flex items-center gap-1">
              {[
                { icon: Github, href: 'https://github.com/PasanSiriwardana0/', label: 'GitHub' },
                { icon: Linkedin, href: '#', label: 'LinkedIn' },
                { icon: Mail, href: '#contact', label: 'Email' },
              ].map(({ icon: Icon, href, label }) => (
                <a
                  key={label}
                  href={href}
                  aria-label={label}
                  className="relative p-2 rounded-lg border border-border/30 bg-background/50 hover:bg-accent/20 transition-all duration-300 group"
                >
                  <Icon className="size-5 transition-transform duration-300 group-hover:scale-110" />
                  <div className="absolute inset-0 bg-primary/5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
                </a>
              ))}
            </div>

            {/* Mobile Menu Button */}
            <button
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
              className="md:hidden p-2 rounded-lg border border-border/30 bg-background/50 hover:bg-accent/20 transition-all duration-300"
            >
              {isMobileMenuOpen ? <X className="size-5" /> : <Menu className="size-5" />}
            </button>
          </div>
        </div>

        {/* Mobile Navigation */}
        <div
          className={cn(
            'md:hidden overflow-hidden transition-all duration-300',
            isMobileMenuOpen ? 'max-h-64 opacity-100' : 'max-h-0 opacity-0',
          )}
        >
          <nav className="py-4 space-y-2">
            {navItems.map((item) => (
              <button
                key={item.href}
                onClick={() => handleNavClick(item.href)}
                className="w-full text-left px-4 py-3 rounded-lg hover:bg-accent/20 transition-all duration-300 font-medium"
              >
                {item.label}
              </button>
            ))}
          </nav>
        </div>
      </div>
    </header>
  );
}

function Hero() {
  const { isDark } = useContext(ThemeContext);

  return (
    <Section id="hero" className="pt-24">
      <div className="mx-auto w-full max-w-[var(--container-figma)] px-4 md:px-6">
        <div className="relative isolate overflow-hidden rounded-2xl border border-border/60 bg-gradient-to-b from-background/60 to-background/90 p-8 md:p-12 group">
          {/* Animated background elements */}
          <div className="absolute inset-0 -z-10 bg-[conic-gradient(from_180deg_at_50%_50%,theme(colors.primary/20),transparent)] animate-pulse" />
          <div className="absolute -inset-10 bg-gradient-to-r from-primary/10 via-purple-600/10 to-transparent opacity-0 group-hover:opacity-100 blur-xl transition-all duration-1000" />

          <div className="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
            <div className="max-w-2xl">
              <div className="inline-flex items-center gap-2 rounded-full border border-border/60 bg-background/80 px-3 py-1 text-xs text-muted-foreground mb-4 animate-float">
                <Sparkles className="size-4 text-primary animate-pulse" />
                Blockchain Engineer
              </div>

              <h1 className="mt-4 text-balance bg-gradient-to-br from-primary via-foreground to-foreground/60 bg-clip-text text-5xl font-extrabold tracking-tight text-transparent md:text-6xl animate-slide-up">
                I'm Pasan Siriwardana
              </h1>

              <p className="mt-4 max-w-prose text-muted-foreground animate-slide-up delay-100">
                I design and engineer performant, accessible, and beautiful web experiences using TypeScript, React, and modern tooling.
              </p>

              <div className="mt-6 flex flex-wrap gap-3 animate-slide-up delay-200">
                <Button className="group relative overflow-hidden" asChild>
                  <a href="#projects">
                    <Rocket className="mr-1.5 size-4 transition-transform duration-300 group-hover:translate-y-[-2px]" />
                    Explore Projects
                    <div className="absolute inset-0 bg-gradient-to-r from-primary/20 to-purple-600/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300" />
                  </a>
                </Button>
                <Button variant="outline" className="group relative overflow-hidden" asChild>
                  <a href={cvPdf} download>
                    <Download className="mr-1.5 size-4 transition-transform duration-300 group-hover:translate-y-[-2px]" />
                    Download CV
                    <div className="absolute inset-0 bg-accent/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300" />
                  </a>
                </Button>
              </div>
            </div>

            <div className="relative mt-8 aspect-square w-full max-w-md self-center md:mt-0 group/card">
              <div
                className={cn(
                  'absolute inset-0 rounded-[2rem] blur-2xl transition-all duration-1000',
                  isDark
                    ? 'bg-gradient-to-tr from-primary/40 via-purple-600/30 to-transparent'
                    : 'bg-gradient-to-tr from-primary/30 via-purple-500/20 to-transparent',
                )}
              />
              <div className="relative grid h-full place-items-center rounded-[2rem] border border-border/60 bg-background/80 p-6 shadow-inner hover:shadow-xl transition-all duration-300">
                <div className="rounded-xl border border-border/60 bg-gradient-to-b from-background/60 to-background p-6 text-center transform transition-transform duration-300">
                  <Code2 className="mx-auto size-12 text-primary mb-3 animate-bounce" />
                  <p className="text-sm text-muted-foreground">Crafted with React • TypeScript • Tailwind</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Section>
  );
}

// Add CSS animations (you can add this to your global CSS or use a style tag)
const styles = `
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-5px); }
}

@keyframes slide-up {
  0% { 
    opacity: 0;
    transform: translateY(30px);
  }
  100% { 
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-float {
  animation: float 3s ease-in-out infinite;
}

.animate-slide-up {
  animation: slide-up 0.6s ease-out forwards;
}

.delay-100 {
  animation-delay: 0.1s;
  opacity: 0;
}

.delay-200 {
  animation-delay: 0.2s;
  opacity: 0;
}

.delay-300 {
  animation-delay: 0.3s;
  opacity: 0;
}
`;

function App() {
  const [isDark, setIsDark] = useState(false);
  // If user explicitly toggled theme, we respect it and stop adapting to system
  const [hasUserPref, setHasUserPref] = useState(false);

  useEffect(() => {
    // Initialize theme from cookie > localStorage > system
    const cookieTheme = (getCookie('theme') as ThemeCookie | null) || (localStorage.getItem('theme') as ThemeCookie | null);
    const mql = window.matchMedia('(prefers-color-scheme: dark)');

    if (cookieTheme === 'dark' || cookieTheme === 'light') {
      setIsDark(cookieTheme === 'dark');
      setHasUserPref(true);
    } else {
      // Default to system and keep adapting
      setIsDark(mql.matches);
      setHasUserPref(false);
      setCookie('theme', 'system');
    }
  }, []);

  useEffect(() => {
    // Apply theme to document and persist preference
    document.documentElement.classList.toggle('dark', isDark);

    if (hasUserPref) {
      // Persist explicit preference
      const val = isDark ? 'dark' : 'light';
      localStorage.setItem('theme', val);
      setCookie('theme', val);
    } else {
      // Persist that we are in system/adaptive mode
      setCookie('theme', 'system');
      // Ensure no stale explicit preference in localStorage
      localStorage.removeItem('theme');
    }
  }, [isDark, hasUserPref]);

  const toggleTheme = () => {
    setHasUserPref(true);
    setIsDark((prev) => !prev);
  };

  // Adapt to OS theme changes when user has not explicitly chosen
  useEffect(() => {
    const mql = window.matchMedia('(prefers-color-scheme: dark)');
    const handle = (e: MediaQueryListEvent) => {
      if (!hasUserPref) setIsDark(e.matches);
    };
    mql.addEventListener('change', handle);
    return () => mql.removeEventListener('change', handle);
  }, [hasUserPref]);

  // Add smooth scroll behavior for in-page anchors
  useEffect(() => {
    const handleSmoothScroll = (e: MouseEvent) => {
      const target = e.target as HTMLElement | null;
      if (!target) return;
      const anchor = target.closest('a[href^="#"]') as HTMLAnchorElement | null;
      if (!anchor) return;
      const href = anchor.getAttribute('href');
      if (href && href.startsWith('#')) {
        const el = document.querySelector(href);
        if (el) {
          e.preventDefault();
          el.scrollIntoView({ behavior: 'smooth' });
        }
      }
    };

    document.addEventListener('click', handleSmoothScroll);
    return () => document.removeEventListener('click', handleSmoothScroll);
  }, []);

  return (
    <ThemeContext.Provider value={{ isDark, toggleTheme }}>
      <style>{styles}</style>
      <div
        className={cn(
          'relative min-h-screen bg-background text-foreground transition-colors duration-300',
          isDark ? 'dark' : '',
        )}
      >
        <NeonBackground />
        <Header />
        <main>
          <Hero />
          <About />
          <Skills />
          <Projects />
          <Experience />
          <Contact />
        </main>
        <Footer />
      </div>
    </ThemeContext.Provider>
  );
}

function About() {
  return (
    <Section id="about">
      <div className="mx-auto w-full max-w-[var(--container-figma)] px-4 md:px-6">
        <div className="grid gap-6 md:grid-cols-[1fr_1.2fr] items-start animate-slide-up">
          <div className="rounded-2xl border border-border/60 bg-background/70 p-6">
            <h2 className="text-3xl font-bold">About</h2>
            <p className="mt-3 text-muted-foreground">
              I engineer decentralized applications and blockchain solutions with a focus on scalability, security, and performance. I enjoy transforming innovative ideas into eliable smart contracts and Web3 experiences.
            </p>
          </div>
          <div className="rounded-2xl border border-border/60 bg-background/70 p-6">
            <h3 className="font-semibold">What I value</h3>
            <ul className="mt-3 grid gap-2 text-sm text-muted-foreground">
              <li>Secure and gas-efficient smart contracts</li>
              <li>Scalable decentralized applications (dApps)</li>
              <li>Blockchain architecture & protocol design</li>
            </ul>
          </div>
        </div>
      </div>
    </Section>
  );
}

function Skills() {
  return (
    <Section id="skills">
      <div className="mx-auto w-full max-w-[var(--container-figma)] px-4 md:px-6">
        <div className="flex items-end justify-between gap-4 animate-slide-up">
          <h2 className="text-3xl font-bold">Skills</h2>
        </div>
        <div className="mt-6 flex flex-wrap gap-2">
          {skills.map((s, i) => (
            <span
              key={s}
              className="animate-slide-up rounded-full border border-border/60 bg-background/70 px-3 py-1 text-sm"
              style={{ animationDelay: `${i * 0.06}s` }}
            >
              {s}
            </span>
          ))}
        </div>
      </div>
    </Section>
  );
}

function Projects() {
  return (
    <Section id="projects">
      <div className="mx-auto w-full max-w-[var(--container-figma)] px-4 md:px-6">
        <div className="flex items-end justify-between gap-4 animate-slide-up">
          <h2 className="text-3xl font-bold">Projects</h2>
          <Button variant="ghost" className="group" asChild>
            <a href="#">
              View All
              <ExternalLink className="ml-2 size-4 transition-transform duration-300 group-hover:translate-x-1" />
            </a>
          </Button>
        </div>
        <div className="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {projects.map((p, index) => (
            <a
              key={p.title}
              href={p.link}
              className="group relative block overflow-hidden rounded-2xl border border-border/60 bg-background/70 p-6 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl animate-slide-up"
              style={{ animationDelay: `${index * 0.1}s` }}
            >
              <div className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-primary/10 blur-2xl transition-all duration-500 group-hover:scale-150" />
              <div className="relative">
                <h3 className="text-xl font-semibold transition-colors duration-300 group-hover:text-primary">
                  {p.title}
                </h3>
                <p className="mt-2 text-sm text-muted-foreground">{p.description}</p>
                <div className="mt-4 flex flex-wrap gap-2">
                  {p.tags.map((t) => (
                    <span
                      key={t}
                      className="rounded-full border border-border/60 bg-background/60 px-2.5 py-1 text-xs transition-all duration-300 group-hover:border-primary/30 group-hover:bg-primary/10"
                    >
                      {t}
                    </span>
                  ))}
                </div>
              </div>
            </a>
          ))}
        </div>
      </div>
    </Section>
  );
}

function Experience() {
  return (
    <Section id="experience">
      <div className="mx-auto w-full max-w-[var(--container-figma)] px-4 md:px-6">
        <div className="flex items-end justify-between gap-4 animate-slide-up">
          <h2 className="text-3xl font-bold">Experience</h2>
        </div>
        <div className="mt-6 grid gap-6 md:grid-cols-2">
          {experience.map((e, i) => (
            <div
              key={`${e.company}-${e.role}`}
              className="animate-slide-up rounded-2xl border border-border/60 bg-background/70 p-6"
              style={{ animationDelay: `${i * 0.08}s` }}
            >
              <div className="flex items-center justify-between">
                <h3 className="text-xl font-semibold">{e.role}</h3>
                <span className="text-xs text-muted-foreground">{e.period}</span>
              </div>
              <p className="mt-1 text-sm text-muted-foreground">{e.company}</p>
              <p className="mt-3 text-sm text-muted-foreground">{e.details}</p>
            </div>
          ))}
        </div>
      </div>
    </Section>
  );
}

function Contact() {
  return (
    <Section id="contact">
      <div className="mx-auto w-full max-w-[var(--container-figma)] px-4 md:px-6">
        <div className="flex items-end justify-between gap-4 animate-slide-up">
          <h2 className="text-3xl font-bold">Contact</h2>
        </div>
        <div className="mt-6 grid gap-6 md:grid-cols-2">
          <div className="rounded-2xl border border-border/60 bg-background/70 p-6 animate-slide-up">
            <h3 className="font-semibold">Get in touch</h3>
            <p className="mt-2 text-sm text-muted-foreground">
              Open to opportunities, collaborations, and interesting problems.
            </p>
            <div className="mt-4 flex items-center gap-2">
              <Button asChild>
                <a href="mailto:hello@example.com">
                  <Mail className="mr-2 size-4" />
                  Email me
                </a>
              </Button>
              <Button variant="outline" asChild>
                <a href="#">
                  <Github className="mr-2 size-4" />
                  GitHub
                </a>
              </Button>
              <Button variant="outline" asChild>
                <a href="#">
                  <Linkedin className="mr-2 size-4" />
                  LinkedIn
                </a>
              </Button>
            </div>
          </div>
          <div className="rounded-2xl border border-border/60 bg-background/70 p-6 animate-slide-up delay-100">
            <h3 className="font-semibold">Availability</h3>
            <p className="mt-2 text-sm text-muted-foreground">
              Currently available for freelance and contract work.
            </p>
          </div>
        </div>
      </div>
    </Section>
  );
}

function Footer() {
  return (
    <footer className="border-t border-border/60 py-10">
      <div className="mx-auto w-full max-w-[var(--container-figma)] px-4 md:px-6">
        <div className="flex flex-col items-center justify-between gap-4 md:flex-row">
          <p className="text-sm text-muted-foreground">
            © {new Date().getFullYear()}. All rights reserved.
          </p>
          <div className="flex items-center gap-2">
            {[
              { icon: Github, href: '#', label: 'GitHub' },
              { icon: Linkedin, href: '#', label: 'LinkedIn' },
              { icon: Mail, href: '#contact', label: 'Email' },
            ].map(({ icon: Icon, href, label }) => (
              <a
                key={label}
                href={href}
                className="relative p-2 rounded-lg border border-border/30 bg-background/50 hover:bg-accent/20 transition-all duration-300 group"
              >
                <Icon className="size-5 transition-transform duration-300 group-hover:scale-110" />
                <div className="absolute inset-0 bg-primary/5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
              </a>
            ))}
          </div>
        </div>
      </div>
    </footer>
  );
}

export default App;
