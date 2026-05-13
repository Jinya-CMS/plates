import {themes} from "prism-react-renderer";
import type {Config} from '@docusaurus/types';
import type * as Preset from '@docusaurus/preset-classic';

const config: Config = {
    title: 'Plates Template Engine',
    tagline: 'Plates, the native PHP template system that\'s fast, easy to use and easy to extend.',
    favicon: 'img/favicon.svg',
    // Set the production url of your site here
    url: 'https://plates.jinya.dev',
    // Set the /<baseUrl>/ pathname under which your site is served
    // For GitHub pages deployment, it is often '/<projectName>/'
    baseUrl: '/',

    // GitHub pages deployment config.
    // If you aren't using GitHub pages, you don't need these.
    organizationName: 'jinya-cms', // Usually your GitHub org/user name.
    projectName: 'plates', // Usually your repo name.

    onBrokenLinks: 'throw',

    // Even if you don't use internalization, you can use this field to set useful
    // metadata like html lang. For example, if your site is Chinese, you may want
    // to replace "en" with "zh-Hans".
    i18n: {
        defaultLocale: 'en',
        locales: ['en'],
    },

    presets: [
        [
            'classic',
            {
                docs: {
                    sidebarPath: './sidebars.ts',
                },
                theme: {
                    customCss: './src/css/custom.css',
                },
            } satisfies Preset.Options,
        ],
    ],

    themeConfig: {
        image: 'img/social-card.jpg',
        navbar: {
            title: 'Plates',
            logo: {
                alt: 'Plates',
                src: 'img/favicon.svg',
            },
            items: [
                {
                    to: 'docs/intro',
                    activeBasePath: 'docs',
                    label: 'Documentation',
                    position: 'left',
                },
                {
                    href: 'https://gitlab.imanuel.dev/jinya-cms/plates/',
                    label: 'GitLab',
                    position: 'right',
                },
                {
                    href: 'https://github.com/jinya-cms/plates/',
                    label: 'Github',
                    position: 'right',
                },
            ],
        },
        footer: {
            style: 'dark',
            links: [
                {
                    title: 'Documentation',
                    items: [
                        {
                            label: 'Documentation',
                            to: '/docs/intro',
                        },
                    ],
                },
                {
                    title: 'Community',
                    items: [
                        {
                            label: 'Stack Overflow',
                            href: 'https://stackoverflow.com/questions/tagged/jinya-cms',
                        },
                        {
                            label: 'Website',
                            href: 'https://jinya.de',
                        },
                    ],
                },
                {
                    title: 'More',
                    items: [
                        {
                            href: 'https://gitlab.imanuel.dev/jinya-cms/plates/',
                            label: 'GitLab',
                        },
                        {
                            label: 'GitHub',
                            href: 'https://github.com/jinya-cms/plates',
                        },
                    ],
                },
            ],
            copyright: `Copyright © ${new Date().getFullYear()} Jinya Developers. Built with Docusaurus.`,
        },
        prism: {
            theme: themes.github,
            darkTheme: themes.dracula,
            additionalLanguages: ['php', 'php-extras', 'bash'],
        },
    } satisfies Preset.ThemeConfig,
};

export default config;
