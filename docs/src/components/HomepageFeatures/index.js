import React from 'react';
import clsx from 'clsx';
import styles from './styles.module.css';

const FeatureList = [
  {
    title: 'Easy to Use',
    description: (
      <>
        Plates templates are just plain PHP files, you only need to know one language. The language you already use, PHP.
      </>
    ),
  },
  {
    title: 'Works in any project',
    description: (
      <>
        Plates works in any PHP project, be it a CLI app or a CMS. You can use it for emails and for HTML files, it is just PHP.
      </>
    ),
  },
  {
    title: 'Easy to extend',
    description: (
      <>
        You need additional functionality? Just add it, Plates has a rich extension system.
      </>
    ),
  },
];

function Feature({title, description}) {
  return (
    <div className={clsx('col col--4')}>
      <div className="text--center padding-horiz--md">
        <h3>{title}</h3>
        <p>{description}</p>
      </div>
    </div>
  );
}

export default function HomepageFeatures() {
  return (
    <section className={styles.features}>
      <div className="container">
        <div className="row">
          {FeatureList.map((props, idx) => (
            <Feature key={idx} {...props} />
          ))}
        </div>
      </div>
    </section>
  );
}
